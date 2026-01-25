<?php
class Morador{
    private $id_morador;
    private $id_usuario;
    private $id_unidade;

    public function setMorador($id_morador){$this->id_morador = $id_morador;}
    public function setUsuario($id_usuario){$this->id_usuario = $id_usuario;}
    public function setUnidade($id_unidade){$this->id_unidade = $id_unidade;}

    public function salvarMorador($conn){
        $sql = "INSERT INTO Morador(id_usuario) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $this->id_usuario);
        return $stmt->execute();
    }
}