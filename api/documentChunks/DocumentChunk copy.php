<?php

class DocumentChunk {
    private $id;
    private $document_id;
    private $chunk_text;
    private $embedding_vector;
    private $department_name;
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

    public function getDepartmentName() {
        return $this->department_name;
    }
    public function setDepartmentName($department_name) {
        $this->department_name = $department_name;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function userHasAccessByDepartmentName($requested_department_name) {
        return strtolower(trim($this->department_name)) === strtolower(trim($requested_department_name));
    }

    public function getChunkTextForDepartmentName($requested_department_name) {
        if ($this->userHasAccessByDepartmentName($requested_department_name)) {
            return $this->chunk_text;
        } else {
            return "You do not have permission to access this information.";
        }
    }
}
