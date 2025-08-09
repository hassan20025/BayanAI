<?php
require_once "DocumentChunk.php";
require_once "../../db/db.php";

global $mysqli;

function find_all_document_chunks(): array {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM document_chunks");
    $chunks = [];

    while ($row = $result->fetch_assoc()) {
        $chunk = new DocumentChunk();
        $chunk->setId($row["id"]);
        $chunk->setDocumentId($row["document_id"]);
        $chunk->setChunkText($row["chunk_text"]);
        $chunk->setEmbeddingVector(json_decode($row["embedding_vector"], true));
        $chunk->setDepartmentName($row["department"]);
        $chunk->setCreatedAt($row["created_at"]);

        $chunks[] = $chunk;
    }

    return $chunks;
}

function find_document_chunk_by_id(int $id): ?DocumentChunk {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM document_chunks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $chunk = new DocumentChunk();
        $chunk->setId($row["id"]);
        $chunk->setDocumentId($row["document_id"]);
        $chunk->setChunkText($row["chunk_text"]);
        $chunk->setEmbeddingVector(json_decode($row["embedding_vector"], true));
        $chunk->setDepartmentName($row["department"]);
        $chunk->setCreatedAt($row["created_at"]);
        return $chunk;
    }

    return null;
}

function create_document_chunk(DocumentChunk $chunk): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO document_chunks (document_id, chunk_text, embedding_vector, department) VALUES (?, ?, ?, ?)");
    $documentId = $chunk->getDocumentId();
    $text = $chunk->getChunkText();
    $embedding = json_encode($chunk->getEmbeddingVector());
    $department_name = $chunk->getDepartmentName();
    $stmt->bind_param("isss", $documentId, $text, $embedding, $department_name);
    return $stmt->execute();
}

function update_document_chunk(DocumentChunk $chunk): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE document_chunks SET document_id = ?, chunk_text = ?, embedding_vector = ?, department = ? WHERE id = ?");
    $documentId = $chunk->getDocumentId();
    $text = $chunk->getChunkText();
    $embedding = json_encode($chunk->getEmbeddingVector());
    $department_name = $chunk->getDepartmentName();
    $id = $chunk->getId();
    $stmt->bind_param("isssi", $documentId, $text, $embedding, $department_name, $id);
    return $stmt->execute();
}

function delete_document_chunk_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM document_chunks WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
