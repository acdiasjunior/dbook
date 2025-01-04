<?php
declare (strict_types = 1);

namespace App\Command;

use Cake\Cache\Cache;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;

class ImportBooksCommand extends Command
{

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('Starting book import...');

        // Load tables
        $booksTable        = TableRegistry::getTableLocator()->get('Books');
        $villainsTable     = TableRegistry::getTableLocator()->get('Villains');
        $bookVillainsTable = TableRegistry::getTableLocator()->get('BookVillains');

        $http = new Client();

        $response = $http->get('https://stephen-king-api.onrender.com/api/books');

        if (!$response->isOk()) {
            $io->err('Failed to fetch books from the API.');
            return Command::CODE_ERROR;
        }

        $books = $response->getJson();

        $importedBooks = 0;

        foreach ($books['data'] as $bookData) {
            // Workaround for a wrong ISBN on the Green Mile Book
            $bookData['ISBN'] = substr($bookData['ISBN'], 0, 17);

            $book = Cache::remember('villain_' . md5($bookData['ISBN']), function () use ($booksTable, $bookData) {
                return $booksTable->find()
                    ->where(['isbn' => $bookData['ISBN']])
                    ->first();
            });

            if (!$book) {
                $book = $booksTable->newEntity([
                    'title'      => $bookData['Title'],
                    'year'       => $bookData['Year'],
                    'handle'     => $bookData['handle'],
                    'publisher'  => $bookData['Publisher'],
                    'isbn'       => $bookData['ISBN'],
                    'pages'      => $bookData['Pages'],
                    'notes'      => json_encode($bookData['Notes']),
                    'created_at' => date('Y-m-d H:i:s', strtotime($bookData['created_at'])),
                ]);
            }

            if ($booksTable->save($book)) {
                Cache::write('book_' . md5($bookData['ISBN']), $book);

                $importedBooks++;

                $io->out("Imported book: {$bookData['Title']}");

                foreach ($bookData['villains'] as $villainData) {
                    $villain = Cache::remember('villain_' . md5($villainData['name']), function () use ($villainsTable, $villainData) {
                        return $villainsTable->find()
                            ->where(['name' => $villainData['name']])
                            ->first();
                    });

                    if (!$villain) {
                        $villain = $villainsTable->newEntity([
                            'name' => $villainData['name'],
                            'url'  => $villainData['url'],
                        ]);

                        if ($villainsTable->save($villain)) {
                            Cache::write('villain_' . md5($villainData['name']), $villain);
                            $io->out(" - Added new villain: {$villainData['name']}");
                        } else {
                            $io->err(" - Failed to save villain: {$villainData['name']}");
                            continue;
                        }
                    }

                    $bookVillain = Cache::remember('book_villain_' . $book->id . '_' . $villain->id, function () use ($bookVillainsTable, $book, $villain) {
                        return $bookVillainsTable->find()
                            ->where([
                                'book_id'    => $book->id,
                                'villain_id' => $villain->id,
                            ])
                            ->first();
                    });

                    if (!$bookVillain) {
                        $bookVillain = $bookVillainsTable->newEntity([
                            'book_id'    => $book->id,
                            'villain_id' => $villain->id,
                        ]);

                        if ($bookVillainsTable->save($bookVillain)) {
                            $io->out(" - Associated villain: {$villainData['name']} with book: {$bookData['Title']}");
                            Cache::write('book_villain_' . $book->id . '_' . $villain->id, $bookVillain);
                        } else {
                            $io->err(" - Failed to associate villain: {$villainData['name']} with book: {$bookData['Title']}");
                        }
                    }
                }
            } else {
                $errors = $book->getErrors();
                if (!empty($errors)) {
                    foreach ($errors as $field => $fieldErrors) {
                        foreach ($fieldErrors as $error) {
                            $io->err("Error in field '{$field}': {$error}");
                        }
                    }
                } else {
                    $io->err("Failed to import book: {$bookData['Title']}");
                }
            }
        }

        Cache::clear();

        $io->success("Book import completed. Total books imported: {$importedBooks}");

        return Command::CODE_SUCCESS;
    }
}
