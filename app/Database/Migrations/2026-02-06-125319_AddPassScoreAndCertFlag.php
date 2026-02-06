<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPassScoreAndCertFlag extends Migration
{
    public function up()
    {
        $this->forge->addColumn('quizzes', [
            'pass_score' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 70,
                'after' => 'total_questions'
            ],
        ]);

        $this->forge->addColumn('quiz_assignments', [
            'certificate_sent' => [
                'type' => 'BOOLEAN',
                'default' => 0,
                'after' => 'result_email_sent'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('quizzes', 'pass_score');
        $this->forge->dropColumn('quiz_assignments', 'certificate_sent');
    }
}
