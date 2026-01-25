<?php
class Sindico{
    private $id_sindico;
    private $id_usuario;
    private $id_unidade;

    public function setSindico($id_sindico){$this->id_sindico = $id_sindico;}
    public function setUsuario($id_usuario){$this->id_usuario = $id_usuario;}
    public function setUnidade($id_unidade){$this->id_unidade = $id_unidade;}

    public function salvarSindico($conn){
        $sql = "INSERT INTO Sindico(id_usuario) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $this->id_usuario);
        return $stmt->execute();
    }
}