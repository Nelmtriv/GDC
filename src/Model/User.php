<?php

class User{
    private $nome;
    private $telefone;
    private $email;
	private $senha_hash;
    private $nacionalidade;
	private $documento;
    private $tipo_documento;
    private $tipo;
    private $docPath;

    public function setNome($nome){$this->nome = $nome;}
    public function setTelefone($telefone){$this->telefone = $telefone;}
    public function setEmail($email){$this->email = $email;}
	public function setSenha_hash($senha_hash){$this->senha_hash = $senha_hash;}
    public function setNacionalidade($nacionalidade){$this->nacionalidade = $nacionalidade;}
	public function setDocumento($documento){$this->documento = $documento;}
    public function setTipo_documento($tipo_documento){$this->tipo_documento = $tipo_documento;}
    public function setTipo($tipo){$this->tipo = $tipo;}
    public function setDocPath(string $docPath){
        $this->docPath = $docPath;
    }

    public function salvar($conn){
        $sql = "INSERT INTO Usuario (
                            nome,
                            telefone,
                            email,
                            senha_hash,
                            nacionalidade,
                            documento,
                            tipo_documento,
                            tipo,
                            docPath)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss",
            $this->nome,
            $this->telefone,
            $this->email,
            $this->senha_hash,
            $this->nacionalidade,
            $this->documento,
            $this->tipo_documento,
            $this->tipo,
            $this->docPath
        );
        return $stmt->execute();
    }

    public function VerifyUser($conn){
        $sql = "SELECT id_usuario FROM Usuario WHERE email = ?";
        $stmt = $conn->prepare($sql);
        try {
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                throw new Exception("E-mail já cadastrado");
            }

            return false;
        } finally {
            $stmt->close();
        }
    }

    public function LastIdInsert($conn){
        return $conn->insert_id;
    }
}