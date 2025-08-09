<?php
class Department implements JsonSerializable {
    private ?int $id;
    private string $name;

    public function __construct(?int $id = null, string $name = "") {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
