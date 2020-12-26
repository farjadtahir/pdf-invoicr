<?php

namespace Konekt\PdfInvoice\Tests;

use Konekt\PdfInvoice\InvoicePrinter;
use PHPUnit\Framework\TestCase;

class CreateInvoiceTest extends TestCase
{
    /** @test */
    public function createInvoice()
    {
        $invoice = new InvoicePrinter();
        $invoice->setLogo(__DIR__ . "/../examples/images/sample1.jpg");
        $invoice->setColor("#007fff");
        $invoice->setType("Sale Invoice");
        $invoice->setReference("INV-55033645");
        $invoice->setDate(date('M dS ,Y', time()));
        $invoice->setTime(date('h:i:s A', time()));
        $invoice->setDue(date('M dS ,Y', strtotime('+3 months')));
        $invoice->setFrom(array("Seller Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740","United States of America"));
        $invoice->setTo(array("Purchaser Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740","United States of America"));
        $invoice->addItem("AMD Athlon X2DC-7450", "2.4GHz/1GB/160GB/SMP-DVD/VB", 6, 0, 580, 0, 3480);
        $invoice->addItem("PDC-E5300", "2.6GHz/1GB/320GB/SMP-DVD/FDD/VB", 4, 0, 645, 0, 2580);
        $invoice->addItem('LG 18.5" WLCD', "", 10, 0, 230, 0, 2300);
        $invoice->addItem("HP LaserJet 5200", "", 1, 0, 1100, 0, 1100);
        $invoice->addTotal("Total", 9460);
        $invoice->addTotal("VAT 21%", 1986.6);
        $invoice->addTotal("Total due", 11446.6, true);
        $invoice->addBadge("Payment Paid");
        $invoice->addTitle("Important Notice");
        $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you. You can refund within 2 days of purchase.");
        $invoice->setFooternote("My Company Name Here");
        $pdfInvoice = $invoice->render('example1.pdf', 'S');
        $this->assertNotEmpty($pdfInvoice);
        $this->assertTrue(gettype($pdfInvoice) == 'string');
    }
}
