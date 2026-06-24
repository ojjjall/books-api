<?php

namespace App\Controllers;

use App\Repositories\BookRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class BookController
{
    public function __construct(private BookRepository $books) {}

    // GET /api/books
    public function index(Request $req, Response $res): Response
    {
        $params = $req->getQueryParams();
        $q = (string)($params['q'] ?? '');
        $limit = (int)($params['limit'] ?? 0);

        $rows = $this->books->all($q, $limit);
        return $this->json($res, ['count' => count($rows), 'data' => $rows]);
    }

    // GET /api/books/{id}
    public function show(Request $req, Response $res, array $args): Response
    {
        $book = $this->books->find((int)$args['id']);
        
        if (!$book) {
            return $this->json($res, ['error' => 'Book not found'], 404);
        }
        
        return $this->json($res, $book);
    }

    // POST /api/books
    public function create(Request $req, Response $res): Response
    {
        $body = (array)$req->getParsedBody();
        $errors = $this->validate($body, requireAll: true);
        
        if (!empty($errors)) {
            return $this->json($res, ['errors' => $errors], 400);
        }

        $id = $this->books->create($body);
        $newBook = $this->books->find($id);
        
        return $this->json($res, ['message' => 'Book created', 'data' => $newBook], 201)
                    ->withHeader('Location', '/api/books/' . $id);
    }

    // PUT /api/books/{id}
    public function update(Request $req, Response $res, array $args): Response
    {
        $id = (int)$args['id'];
        
        // Check if book exists
        if (!$this->books->find($id)) {
            return $this->json($res, ['error' => 'Book not found'], 404);
        }

        $body = (array)$req->getParsedBody();
        $errors = $this->validate($body, requireAll: false);
        
        if (!empty($errors)) {
            return $this->json($res, ['errors' => $errors], 400);
        }

        $rowsUpdated = $this->books->update($id, $body);
        
        if ($rowsUpdated === 0 && empty($body)) {
            return $this->json($res, ['message' => 'No changes made'], 200);
        }
        
        return $this->json($res, ['message' => 'Book updated', 'data' => $this->books->find($id)]);
    }

    // DELETE /api/books/{id}
    public function delete(Request $req, Response $res, array $args): Response
    {
        //  ROLE CHECK — only admins can delete
        $auth = (array)$req->getAttribute('auth', []);
        if (($auth['role'] ?? 'member') !== 'admin') {
            return $this->json($res, ['error' => 'Admins only'], 403);
        }

        $id = (int)$args['id'];

        if (!$this->books->find($id)) {
            return $this->json($res, ['error' => 'Book not found'], 404);
        }

        $this->books->delete($id);
        return $this->json($res, ['message' => "Book {$id} deleted"]);
    }
    // Validation helper
    private function validate(array $data, bool $requireAll = false): array
    {
        $errors = [];
        
        if ($requireAll || isset($data['title'])) {
            if (empty(trim($data['title'] ?? ''))) {
                $errors['title'] = 'Title is required';
            }
        }
        
        if ($requireAll || isset($data['author'])) {
            if (empty(trim($data['author'] ?? ''))) {
                $errors['author'] = 'Author is required';
            }
        }
        
        if ($requireAll || isset($data['year'])) {
            $year = $data['year'] ?? 0;
            if (!is_numeric($year) || $year < 1000 || $year > date('Y') + 5) {
                $errors['year'] = 'Valid year is required';
            }
        }
        
        return $errors;
    }

    // JSON response helper
    private function json(Response $res, $data, int $status = 200): Response
    {
        $res->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $res->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}