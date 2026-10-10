<?php

namespace App\Mail\RepairOrder;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RepairOrderToClientMail extends Mailable {
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $repairOrder,
    ) {
    }

    public function envelope(): Envelope {
        $repairOrderId = $this->repairOrder['id'];
        $appName = config('app.name');

        return new Envelope(
            subject: "Ordem de Serviço #{$repairOrderId} - {$appName}",
        );
    }

    public function content(): Content {
        return new Content(
            view: 'emails.repair-order',
            with: [
                'repairOrder' => $this->repairOrder,
            ],
        );
    }

    public function attachments(): array {
        $repairOrderId = $this->repairOrder['id'];

        $repairOrderPDF = Attachment::fromData(
            fn (): string => $this->generatePDF(),
            "OS-{$repairOrderId}.pdf",
        )
            ->withMime('application/pdf');

        return [
            $repairOrderPDF,
        ];
    }

    protected function generatePDF(): string {
        $pdf = Pdf::loadView(
            'pdf.repair-order',
            ['repairOrder' => $this->repairOrder],
        )
        ->setPaper('a4', 'portrait')
        ->output();

        return $pdf;
    }
}
