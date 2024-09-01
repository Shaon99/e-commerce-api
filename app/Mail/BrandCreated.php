<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BrandCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $brand;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($brand)
    {
        $this->brand = $brand;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Brand Created')
            ->html($this->buildHtmlContent());
    }

    /**
     * Build the HTML content for the email.
     *
     * @return string
     */
    protected function buildHtmlContent()
    {
        $html = '<!DOCTYPE html>
                 <html>
                 <head>
                     <title>New Brand Created</title>
                 </head>
                 <body>
                     <h1>New Brand Created</h1>
                     <p>A new brand named "' . $this->brand->name . '" has been created.</p>';

        if ($this->brand->file_url) {
            $html .= '<p>Image: <img src="' . htmlspecialchars($this->brand->file_url, ENT_QUOTES) . '" alt="' . htmlspecialchars($this->brand->name, ENT_QUOTES) . '" style="width: 80px; height: 80px;"></p>';
        }

        $html .= '</body>
                 </html>';

        return $html;
    }
}
