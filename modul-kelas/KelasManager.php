<?php
namespace ModulKelas;

class KelasManager {
    private $conn;
    public function __construct($mysqliConnection) {
        $this->conn = $mysqliConnection;
    }
    public function hapusKelas($id) {
        if (!is_numeric($id) || $id <= 0) {
            return ['status' => false, 'pesan' => 'ID tidak valid'];
        }
        $id = (int) $id;
        $query = "DELETE FROM kelas WHERE id = ? AND arsip = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            return ['status' => true, 'pesan' => 'Kelas berhasil dihapus'];
        } else {
            return ['status' => false, 'pesan' => 'Kelas tidak ditemukan atau belum diarsipkan'];
        }
    }
}
