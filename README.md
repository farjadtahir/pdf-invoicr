pdf-invoicr
===========

INTRODUCTION
===========

PHP Invoice is a simple object oriented PHP class to generate beautifully designed invoices, quotes or orders with just a few lines of code. Brand it with your own logo and theme color, add unlimited items and total rows with automatic paging. You can deliver the PDF ouput in the user's browser, save on the server or force a file download. PHP Invoice is fully customizable and can be integrated into any well known CMS.

MULTI-LANGUAGES & CURRENCIES
============================

PHP Invoice has built in translations in English, Dutch, French, German, Spanish and Italian (you can easily add your own if needed) and you can set the currency needed per document.

ADDITIONAL TITLES, PARAGRAPHS AND BADGES

We made it easy to add extra content (titles and multi-line paragraphs) to the bottom of the document. You might use it for payment or shipping information or any other content needed.

GETTING STARTED
===============

INSTALLATION

Upload the php-invoice folder and all content to your webserver.

EXAMPLES

There are 3 examples included in the class that work out of the box.
Surf to http://[yourwebsite]/php-invoice/examples/filename.php to view them.
- simple.php
- example1.php
- example2.php
- change_timezone.php


CREATE A NEW INVOICE
====================

Create a new php file in the root of your webserver and include the class to get started.
Please make sure that the default php date timezone is set before including the class.
Click here for more information.

In this simple example we are generating an invoice with custom logo and theme color. 
It will contain 2 products and a box on the bottom with VAT and total price. Then we add a "Paid" badge right before the output.

<?php
include('../phpinvoice.php');
$invoice = new phpinvoice();
  /* Header Settings */
  $invoice->setLogo("images/sample1.jpg");   //logo image path
  $invoice->setColor("#007fff");      // pdf color scheme
  $invoice->setType("Sale Invoice");    // Invoice Type
  $invoice->setReference("INV-55033645");   // Reference
  $invoice->setDate(date('M dS ,Y',time()));   //Billing Date
  $invoice->setTime(date('h:i:s A',time()));   //Billing Time
  $invoice->setDue(date('M dS ,Y',strtotime('+3 months')));    // Due Date
  $invoice->setFrom(array("Seller Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));
  $invoice->setTo(array("Purchaser Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));
  /* Adding Items in table */
  $invoice->addItem("AMD Athlon X2DC-7450","2.4GHz/1GB/160GB/SMP-DVD/VB",6,0,580,0,3480);
  $invoice->addItem("PDC-E5300","2.6GHz/1GB/320GB/SMP-DVD/FDD/VB",4,0,645,0,2580);
  $invoice->addItem('LG 18.5" WLCD',"",10,0,230,0,2300);
  $invoice->addItem("HP LaserJet 5200","",1,0,1100,0,1100);
  /* Add totals */
  $invoice->addTotal("Total",9460);
  $invoice->addTotal("VAT 21%",1986.6);
  $invoice->addTotal("Total due",11446.6,true);
  /* Set badge */ 
  $invoice->addBadge("Payment Paid");
  /* Add title */
  $invoice->addTitle("Important Notice");
  /* Add Paragraph */
  $invoice->addParagraph("No item will be replaced or refunded if you don't have the invoice with you.");
  /* Set footer note */
  $invoice->setFooternote("My Company Name Here");
  /* Render */
  $invoice->render('example1.pdf','I'); 
  /* I => Display on browser, D => Force Download, F => local path save, S => return document path */
?>
		
DOCUMENTATION
=============

See and learn how every Php Invoice method works in detail

CREATE INSTANCE

Start a new instance of the PHP Invoice class.

$invoice = new phpinvoice(size,currency,language); // Default Param: Size: A4, Currency: $, Language: en
size {string}
Set your document size.
A4Default
Letter
Legal
currency {string}
Set the currency that you want to use by simply passing the currency symbol as a string. (e.g. "$")
language {string}
Select a language that exists in the /languages folder. Create your own translation file or use the included:
EN (English)Default
NL (Dutch)
FR (French)
DE (German)
ES (Spanish)
IT (Italian)
NUMBER FORMATTING

How do you want to show your numbers?

$invoice->setNumberFormat(decimalpoint,seperator);
decimalpoint {string}
Specifies what string to use for decimal point. Commonly used is '.' or ','
seperator {string}
Specifies what string to use for thousands separator. Commonly used is '.' or ','
THEME COLOR

Set a custom color to personalize your invoices.

$invoice->setColor(color);
color {string}
Hexadecimal color code. Example for red: '#FF0000'
LOGO

Add your company logo to the invoice.

$invoice->setLogo(image,maxwidth,maxheight);
image {string}
Local path or remote url of the image file to be used, preferably a good quality transparant png image.
maxwidth {int}Optional
Set the width (in mm) of the bounding box where the image will be fitted in. Maxheight parameter is required.
maxheight {int}Optional
Set the height (in mm) of the bounding box where the image will be fitted in. Maxwidth parameter is required.
DOCUMENT TYPE

Set the type of document you are creating.

$invoice->setType(type);
type {string}
A string with the document type. that will be used for the title in the right top corner of the document (e.g. 'invoice' or 'quote')
REFERENCE

Add your document reference or number

$invoice->setReference(reference);
reference {string}
Document reference that will be displayed in the right top corner of the document (e.g. 'INV29782')
DATE

Set your document date.

$invoice->setDate(date);
date {string}
A string with the document's date (e.g. '').
DUE DATEOPTIONAL

Set your invoice due date.

$invoice->setDue(duedate);
duedate {string}
A string with the document's due date (e.g. '')
COMPANY INFORMATION

Set your company details.

$invoice->setFrom(company);
company {array}
An array with your company details. The first value of the array will be bold on the document so we suggest you to use your company's name. You can add as many lines as you need.

Example:
array('My Company','Address line 1','Address line 2','City and zip','Country','VAT number');
CLIENT INFORMATION

Set your client details.

$invoice->setTo(client);
client {array}
An array with your clients' details. The first value of the array will be bold on the document so we suggest you to use your company's name. You can add as many lines as you need.

Example:
array('My Client','Address line 1','Address line 2','City and zip','Country','VAT number');
FLIPFLOPOPTIONAL

Switch the horizontal positions of your company information and the client information. By default, your company details are on the left.

$invoice->flipflop();
ADD ITEM

Add a new product or service row to your document below the company and client information. PHP Invoice has automatic paging so there is absolutely no limit.

$invoice->addItem(name,description,amount,vat,price,discount,total);
name {string}
A string with the product or service name.
description {string}
A string with the description with multi-line support. Use either <br> or \n to add a line-break.
amount {decimal}
An integer with the amount of this item.
vat {string} or {decimal}
Pass a string (e.g. "21%", or any other text you may like) or a decimal if you want to show an amount instead (e.g. 124.30)
price {decimal}
A decimal for the unit price.
discount {string}, {decimal} or {boolean}Optional
Pass a string (e.g. "10%", or any other text you may like) or a decimal if you want to show an amount instead (e.g. 50.00) If you do not want to give discount just enter the boolean false in this field. Note: the final output will not show a discount column when all of the products haven't set a discount.
total {decimal}
A decimal for the total product or service price.
ADD TOTAL

Add a row below the products and services for calculations and totals. You can add unlimited rows.

$invoice->addTotal(name,value,background);
name {string}
A string for the display name of the total field.
value {decimal}
A decimal for the value.
background {boolean}Optional
Set to true to set the theme color as background color of the row.
ADD BADGEOPTIONAL

Adds a badge to your invoice below the products and services. You can use this for example to display that the invoice has been payed.

$invoice->addBadge(badge);
badge {string}
A string with the text of the badge.
ADD TITLE

You can add titles and paragraphs to display information on the bottom part of your document such as payment details or shipping information.

$invoice->addTitle(title);
title {string}
A string with the title to display in the badge.
ADD PARAGRAPH

You can add titles and paragraphs to display information on the bottom part of your document such as payment details or shipping information.

$invoice->addParagraph(paragraph);
Paragraph {string}
A string with the paragraph text with multi-line support. Use either <br> or \n to add a line-break.
FOOTER

A small text you want to display on the bottom left corner of the document.

$invoice->setFooternote(note);
note {string}
A string with the information you want to display in the footer.
RENDER

Render the invoice.

$invoice->render(name,output);
name {string}
A string with the name of your invoice.
Example: 'invoice.pdf'
output {string}
Choose how you want the invoice to be delivered to the user. The following options are available:
I (Send the file inline to the browser)
D (Send to the browser and force a file download with the name given by name)
F (Save to a local file. Make sure to set pass the path in the name parameter)
S (Return the document as a string)

CONTACT US
==========

For any assitance or feedback please contact us at farjad_tahir@splashpk.com. Developed By: Splashpk

CREDITS
=======

We would like to thank the creators of FPDF to create such an amazing PHP library that makes our work a lot easier.
http://www.fpdf.org/
