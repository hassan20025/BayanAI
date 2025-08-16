<?php
require_once "DocumentChunk.php";
require_once __DIR__ . "/../../db/db.php";

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
        $chunk->setFileName($row["file_name"] ?? null);
        $chunk->setFileType($row["file_type"] ?? null);
        $chunk->setSize($row["size"] ?? null);
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
        $chunk->setFileName($row["file_name"] ?? null);
        $chunk->setFileType($row["file_type"] ?? null);
        $chunk->setSize($row["size"] ?? null);
        return $chunk;
    }

    return null;
}

function create_document_chunk(DocumentChunk $chunk): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO document_chunks (document_id, chunk_text, embedding_vector, department, file_name, file_type, size) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $documentId = $chunk->getDocumentId();
    $text = $chunk->getChunkText();
    $embedding = json_encode($chunk->getEmbeddingVector());
    $department_name = $chunk->getDepartmentName();
    $fileName = $chunk->getFileName();
    $fileType = $chunk->getFileType();
    $size = $chunk->getSize();
    $stmt->bind_param("isssssi", $documentId, $text, $embedding, $department_name, $fileName, $fileType, $size);
    return $stmt->execute();
}

function update_document_chunk(DocumentChunk $chunk): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE document_chunks SET document_id = ?, chunk_text = ?, embedding_vector = ?, department = ?, file_name = ?, file_type = ?, size = ? WHERE id = ?");
    $documentId = $chunk->getDocumentId();
    $text = $chunk->getChunkText();
    $embedding = json_encode($chunk->getEmbeddingVector());
    $department_name = $chunk->getDepartmentName();
    $fileName = $chunk->getFileName();
    $fileType = $chunk->getFileType();
    $size = $chunk->getSize();
    $id = $chunk->getId();
    $stmt->bind_param("isssssii", $documentId, $text, $embedding, $department_name, $fileName, $fileType, $size, $id);
    return $stmt->execute();
}

function delete_document_chunk_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM document_chunks WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
