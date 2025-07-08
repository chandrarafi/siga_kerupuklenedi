<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzinTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'idizin' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'pegawai_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'tanggalmulaiizin' => [
                'type' => 'DATE',
            ],
            'tanggalselesaiizin' => [
                'type' => 'DATE',
            ],
            'selected_dates' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Menyimpan semua tanggal yang dipilih sebagai string dipisahkan koma',
            ],
            'jenisizin' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Sakit, Cuti, Izin, dll',
            ],
            'alasan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'lampiran' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file lampiran/bukti',
            ],
            'statusizin' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 3,
                'comment'    => '1=disetujui, 2=ditolak, 3=menunggu',
            ],
            'keterangan_admin' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Keterangan dari admin',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('idizin');
        $this->forge->addForeignKey('pegawai_id', 'pegawai', 'idpegawai', 'CASCADE', 'CASCADE');
        $this->forge->createTable('izin');
    }

    public function down()
    {
        $this->forge->dropTable('izin');
    }
}
