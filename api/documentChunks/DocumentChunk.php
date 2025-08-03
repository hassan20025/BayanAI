<?php

class DocumentChunk {
    private $id;
    private $document_id;
    private $chunk_text;
    private $embedding_vector;
    private $department_id;
    private $created_at;

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getDocumentId() {
        return $this->document_id;
    }
    public function setDocumentId($document_id) {
        $this->document_id = $document_id;
    }

    public function getChunkText() {
        return $this->chunk_text;
    }
    public function setChunkText($chunk_text) {
        $this->chunk_text = $chunk_text;
    }

    public function getEmbeddingVector() {
        return $this->embedding_vector;
    }
    public function setEmbeddingVector($embedding_vector) {
        $this->embedding_vector = $embedding_vector;
    }

    public function getDepartmentId() {
        return $this->department_id;
    }
    public function setDepartmentId($department_id) {
        $this->department_id = $department_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }
}
