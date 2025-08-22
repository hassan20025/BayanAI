<?php

class DocumentChunk implements JsonSerializable {
    private $id;
    private $document_id;
    private $chunk_text;
    private $embedding_vector;
    private $department_name;
    private $created_at;
    private $fileName;
    private $fileType;
    private $size;

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getDocumentId() { return $this->document_id; }
    public function setDocumentId($document_id) { $this->document_id = $document_id; }

    public function getChunkText() { return $this->chunk_text; }
    public function setChunkText($chunk_text) { $this->chunk_text = $chunk_text; }

    public function getEmbeddingVector() { return $this->embedding_vector; }
    public function setEmbeddingVector($embedding_vector) { $this->embedding_vector = $embedding_vector; }

    public function getDepartmentName() { return $this->department_name; }
    public function setDepartmentName($department_name) { $this->department_name = $department_name; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getFileName() { return $this->fileName; }
    public function setFileName($fileName) { $this->fileName = $fileName; }

    public function getFileType() { return $this->fileType; }
    public function setFileType($fileType) { $this->fileType = $fileType; }

    public function getSize() { return $this->size; }
    public function setSize($size) { $this->size = $size; }

    public function isAllowed($department_name) {
        return $department_name === $this->department_name;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'document_id' => $this->document_id,
            'chunk_text' => $this->chunk_text,
            'embedding_vector' => $this->embedding_vector,
            'department_name' => $this->department_name,
            'created_at' => $this->created_at,
            'file_name' => $this->fileName,
            'file_type' => $this->fileType,
            'size' => $this->size,
        ];
    }
}
