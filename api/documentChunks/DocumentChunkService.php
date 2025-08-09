<?php

require_once "DocumentChunkRepository.php";
require_once "../users/UserService.php";
require_once "../messages/MessageService.php";
require_once "../../utils/utils.php";
require_once "../../utils/gemini.php";

header("Content-Type: application/json");

function create_chunk($chunkText, $departmentName) {
    $embedding = generateEmbeddings($chunkText);

    if (isset($embedding['error'])) {
        echo json_encode($embedding);
        respond(500, "error", ["message" => "Failed to generate embedding"]);
    }

    $chunk = new DocumentChunk();
    $chunk->setChunkText($chunkText);
    $chunk->setEmbeddingVector($embedding);
    $chunk->setDepartmentName($departmentName);

    if (!create_document_chunk($chunk)) {
        respond(500, "error", ["message" => "Failed to create chunk."]);
    }

    respond(201, "success", ["message" => "Chunk created successfully."]);
}

function create_chunks($chunks, $department_name) {
    foreach($chunks as $chunk) {
        $embedding = generateEmbeddings($chunk);

        if (isset($embedding['error'])) {
            echo json_encode($embedding);
            respond(500, "error", ["message" => "Failed to generate embedding"]);
        }
    
        $docChunk = new DocumentChunk();
        $docChunk->setChunkText($chunk);
        $docChunk->setEmbeddingVector($embedding);
        $docChunk->setDepartmentName($department_name);
    
        if (!create_document_chunk($docChunk)) {
            respond(500, "error", ["message" => "Failed to create chunks."]);
        }

    }
        
    respond(201, "success", ["message" => "Chunks created successfully."]);
}

function get_chunk_by_id($id) {
    $chunk = find_document_chunk_by_id($id);
    if (!$chunk) {
        respond(404, "error", ["message" => "Chunk not found."]);
    }

    respond(200, "success", [
        "id" => $chunk->getId(),
        "document_id" => $chunk->getDocumentId(),
        "chunk_text" => $chunk->getChunkText(),
        "embedding_vector" => $chunk->getEmbeddingVector(),
        "department" => $chunk->getDepartmentName(),
        "created_at" => $chunk->getCreatedAt()
    ]);
}

function get_all_chunks() {
    $chunks = find_all_document_chunks();
    $output = [];

    foreach ($chunks as $chunk) {
        $output[] = [
            "id" => $chunk->getId(),
            "document_id" => $chunk->getDocumentId(),
            "chunk_text" => $chunk->getChunkText(),
            "embedding_vector" => $chunk->getEmbeddingVector(),
            "department" => $chunk->getDepartmentName(),
            "created_at" => $chunk->getCreatedAt()
        ];
    }

    respond(200, "success", $output);
}

function update_chunk($id, $newText, $newDepartmentName = null) {
    $chunk = find_document_chunk_by_id($id);
    if (!$chunk) {
        respond(404, "error", ["message" => "Chunk not found."]);
    }

    $embedding = generateEmbeddings($newText);
    if (isset($embedding['error'])) {
        respond(500, "error", ["message" => "Failed to regenerate embedding."]);
    }

    $chunk->setChunkText($newText);
    $chunk->setEmbeddingVector($embedding);
    if ($newDepartmentName !== null) {
        $chunk->setDepartmentName($newDepartmentName);
    }

    if (!update_document_chunk($chunk)) {
        respond(500, "error", ["message" => "Failed to update chunk."]);
    }

    respond(200, "success", ["message" => "Chunk updated successfully."]);
}

function delete_chunk($id) {
    $chunk = find_document_chunk_by_id($id);
    if (!$chunk) {
        respond(404, "error", ["message" => "Chunk not found."]);
    }

    if (!delete_document_chunk_by_id($id)) {
        respond(500, "error", ["message" => "Failed to delete chunk."]);
    }

    respond(200, "success", ["message" => "Chunk deleted successfully."]);
}

function get_similar_text($text, $department_name) {
    global $mysqli;
    $embedding = generateEmbeddings($text);

    $query = "SELECT id, chunk_text, embedding_vector, department FROM document_chunks";
    $result = $mysqli->query($query);

    $results = [];
    $SIMILARITY_THRESHOLD = 0.4;

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $chunkEmbedding = json_decode($row['embedding_vector'], true);
            $similarity = cosineSimilarity($embedding, $chunkEmbedding);
            if ($similarity >= $SIMILARITY_THRESHOLD && ((!$department_name || ! $row["department"] || strtolower(trim($row["department"])) == strtolower(trim($department_name))))) {
                $results[] = [
                    'chunk_text' => $row['chunk_text'],
                    'similarity' => $similarity
                ];
            }
        }
    }

    usort($results, function ($a, $b) {
        return $b['similarity'] <=> $a['similarity'];
    });

    $texts = array_column($results, 'chunk_text');

    return $texts;
}

function get_answer($question, $chatId, $userId) {
    $user = get_user_by_id($userId);
    $data = get_similar_text($question, $user->getDepartment());
    $answer = get_company_info($question, $data, get_chat_messages($userId, $chatId));
    send_message(null, $chatId, $answer, "bot");
    return respond(200, "success", $answer);
}