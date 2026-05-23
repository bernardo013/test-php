<?php

namespace App\Controllers;

use App\Database;

class ContatoController
{
    public static function index(array $params): void
    {
        $db = Database::connection();

        $stmt = $db->prepare('SELECT * FROM transportadoras WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$params['id']]);
        if (!$stmt->fetch()) {
            json(['erro' => 'Transportadora não encontrada'], 404);
        }

        $stmt = $db->prepare('SELECT * FROM contatos_transportadora WHERE id_transportadora = ? ORDER BY nome ASC');
        $stmt->execute([$params['id']]);
        $rows = $stmt->fetchAll();

        json(array_map([self::class, 'format'], $rows));
    }

    public static function show(array $params): void
    {
        $db = Database::connection();

        $stmt = $db->prepare('SELECT * FROM transportadoras WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$params['id']]);
        if (!$stmt->fetch()) {
            json(['erro' => 'Transportadora não encontrada'], 404);
        }

        $stmt = $db->prepare('SELECT * FROM contatos_transportadora WHERE id = ? AND id_transportadora = ?');
        $stmt->execute([$params['cid'], $params['id']]);
        $row = $stmt->fetch();

        if (!$row) {
            json(['erro' => 'Contato não encontrado'], 404);
        }

        json(self::format($row));
    }
    public static function store(array $params): void
    {
        $db = Database::connection();

        $stmt = $db->prepare('SELECT * FROM transportadoras WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$params['id']]);
        if (!$stmt->fetch()) {
            json(['erro' => 'Transportadora não encontrada'], 404);
        }

        $data = body();

        if (empty($data['nome']) || empty($data['email'])) {
            json(['erro' => 'Campos nome e email são obrigatórios'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            json(['erro' => 'Email inválido'], 400);

        }

        $stmt = $db->prepare('SELECT * FROM contatos_transportadora WHERE email = ? AND id_transportadora = ?');
        $stmt->execute([$data['email'], $params['id']]);
        if ($stmt->fetch()) {
            json(['erro' => 'Já existe um contato com este email para esta transportadora'], 409);
        }

        $stmt = $db->prepare('INSERT INTO  contatos_transportadora (id_transportadora, nome, email, telefone, cargo) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $params['id'],
            $data['nome'],
            $data['email'],
            $data['telefone'] ?? null,
            $data['cargo'] ?? null
        ]);

        $id = $db->lastInsertId();

        $stmt = $db->prepare('SELECT * FROM contatos_transportadora WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        json(self::format($row), 201);

    }
    public static function destroy(array $params): void
    {
        $db = Database::connection();

        $stmt = $db->prepare('SELECT * FROM transportadoras WHERE id = ? AND deleted_at IS NULL');
        $stmt->execute([$params['id']]);
        if (!$stmt->fetch()) {
            json(['erro' => 'Transportadora não encontrada'], 404);
        }

        $stmt = $db->prepare('SELECT * FROM contatos_transportadora WHERE id = ? AND id_transportadora = ?');
        $stmt->execute([$params['cid'], $params['id']]);
        if (!$stmt->fetch()) {
            json(['erro' => 'Contato não encontrado'], 404);
        }

        $stmt = $db->prepare('DELETE FROM contatos_transportadora WHERE id = ? AND id_transportadora = ?');
        $stmt->execute([$params['cid'], $params['id']]);

        http_response_code(204);

    }

    private static function format(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'nome' => $row['nome'],
            'email' => $row['email'],
            'telefone' => $row['telefone'],
            'cargo' => $row['cargo'],
        ];
    }
}
