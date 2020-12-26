# Changelog

### 1.7.0
##### 2020-12-26

- X-mas 2020 release 🎄 👑 🦠
- Dropped PHP 5.6 - 7.2 support
- Added PHP 8.0 support
- Added Negative Numbers in Parenthesis feature: Thanks [Tristan Curtis](https://github.com/TCURT15)
- Added changeLanguageTerm function: Thanks [Fabian Pankoke](https://github.com/fabianpnke)
- Added Brazilian language file: Thanks [Saulo Henrique](https://github.com/msaulohenrique)
- Added Romanian language file: Thanks [Sebastian Marinescu](https://github.com/sebastian-marinescu)
- Fixed global namespace import: Thanks [Erwan Nader](https://github.com/ErnadoO)
- Switched from Travis to Github Actions

### 1.6.0
##### 2020-06-21

- Added optional alignment and spacing settings to be set via setNumberFormat
- Fixed "Trying to access array offset on value of type null" when trying to calculate height for description on public function Body()
- Fixed undefined offset exception when to and from array lengths are different.
- Changed to output only a line feed if the address line in both arrays is empty (intentional spacing)
- Fixed the "reference" text overlapping the reference itself (if very long)
- Fixed some Dutch language issues

Thanks [Jaap de Jong](https://github.com/japsen) and [Tahri Ahmed](https://github.com/Ousret) for
the improvements!

### 1.5.0
##### 2019-12-19

- It's possible to change the description font size
- Added option to display the currency after the amount
- Added option to display a price without space between the currency and amount
- Table spacing fixes

### 1.4.0
##### 2019-09-20

- Added option to hide Issuer and Client header row
- Changed: no longer printing empty FROM/TO lines
- Change French 'Total' -> 'Total TTC'

### 1.3.1
##### 2019-04-15

- Dutch translation fix

### 1.3.0
##### 2019-02-14

- Added support for setting badge color

### 1.2.1
##### 2018-12-28

- Spanish translation fix

### 1.2.0
##### 2018-12-20

- Lithuanian language support

### 1.1.5
##### 2018-12-17

- Added missing 'time' key in French

### 1.1.4
##### 2018-11-03

- Fix wrong column number when having discount but not VAT.

### 1.1.3
##### 2018-10-17

- Fixed missing `time` entry in German language file

### 1.1.2
##### 2018-10-02

- Minor code and doc improvements

### 1.1.1
##### 2018-08-09

- Polish language support
- UTF-8 support in company name

### 1.1.0
##### 2018-07-11

- The `render()` method returns the output
- Bugfixes

### 1.0.5
##### 2018-07-06

- Turkish translation has been added

### 1.0.4
##### 2018-06-26

- French language improvements
- FIXED: Uppercase doesn't work on accented characters

### 1.0.3
##### 2018-06-06

- German language improvements

### 1.0.2
##### 2018-03-05

- Old classname bugfix


### 1.0.1
##### 2018-01-16

- Attributes have been made public

### 1.0.0
##### 2017-12-15

- Forked latest version from [pdf-invoicr](https://github.com/farjadtahir/pdf-invoicr)
- Converted to PSR-4 and composer
- PHP 7 support added
- FPDF 1.8.1 supported
