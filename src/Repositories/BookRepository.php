<?php

namespace App\Repositories;

use PDO;

final class BookRepository
{
    public function __construct(private PDO $pdo) {}

    // Get all books with optional search and limit
    public function all(string $q = '', int $limit = 0): array
    {
        $sql = 'SELECT * FROM books';
        $args = [];

        if ($q !== '') {
            $sql .= ' WHERE title LIKE :q_title OR author LIKE :q_author';
            $args[':q_title'] = '%' . $q . '%';
            $args[':q_author'] = '%' . $q . '%';
        }

        $sql .= ' ORDER BY id ASC';

        if ($limit > 0) {
            $sql .= ' LIMIT ' . max(1, $limit);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll();
    }

    // Find a single book by ID
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    // Create a new book
    public function create(array $data): int
    {
        $sql = 'INSERT INTO books (title, author, year, genre) 
                VALUES (:title, :author, :year, :genre)';
        
        $this->pdo->prepare($sql)->execute([
            ':title' => trim($data['title']),
            ':author' => trim($data['author']),
            ':year' => (int)$data['year'],
            ':genre' => trim($data['genre'] ?? 'Uncategorized'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    // Update a book (partial update allowed)
    public function update(int $id, array $data): int
    {
        $sets = [];
        $args = [':id' => $id];

        if (array_key_exists('title', $data)) {
            $sets[] = 'title = :title';
            $args[':title'] = trim($data['title']);
        }
        if (array_key_exists('author', $data)) {
            $sets[] = 'author = :author';
            $args[':author'] = trim($data['author']);
        }
        if (array_key_exists('year', $data)) {
            $sets[] = 'year = :year';
            $args[':year'] = (int)$data['year'];
        }
        if (array_key_exists('genre', $data)) {
            $sets[] = 'genre = :genre';
            $args[':genre'] = trim($data['genre']);
        }

        if (empty($sets)) {
            return 0;  // nothing to update
        }

        $sql = 'UPDATE books SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    // Delete a book
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() === 1;
    }
}