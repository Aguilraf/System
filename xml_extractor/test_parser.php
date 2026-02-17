<?php

use App\Services\XmlParserService;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="4.0" Serie="A" Folio="12345" Fecha="2023-10-27T12:00:00" SubTotal="1000.00" Total="1160.00" Moneda="MXN" TipoDeComprobante="I" Exportacion="01" MetodoPago="PUE" LugarExpedicion="01000">
  <cfdi:Emisor Rfc="TEST010203001" Nombre="EMPRESA DE PRUEBA SA DE CV" RegimenFiscal="601"/>
  <cfdi:Receptor Rfc="XAXX010101000" Nombre="PUBLICO EN GENERAL" DomicilioFiscalReceptor="01000" RegimenFiscalReceptor="616" UsoCFDI="S01"/>
  <cfdi:Conceptos>
    <cfdi:Concepto ClaveProdServ="01010101" NoIdentificacion="001" Cantidad="1" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Producto 1" ValorUnitario="500.00" Importe="500.00" ObjetoImp="02">
      <cfdi:Impuestos>
        <cfdi:Traslados>
          <cfdi:Traslado Base="500.00" Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="80.00"/>
        </cfdi:Traslados>
      </cfdi:Impuestos>
    </cfdi:Concepto>
    <cfdi:Concepto ClaveProdServ="01010101" NoIdentificacion="002" Cantidad="1" ClaveUnidad="H87" Unidad="Pieza" Descripcion="Producto 2" ValorUnitario="500.00" Importe="500.00" ObjetoImp="02">
      <cfdi:Impuestos>
        <cfdi:Traslados>
          <cfdi:Traslado Base="500.00" Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="80.00"/>
        </cfdi:Traslados>
      </cfdi:Impuestos>
    </cfdi:Concepto>
  </cfdi:Conceptos>
</cfdi:Comprobante>
XML;

$parser = new XmlParserService();
$data = $parser->parse($xml);

echo "Parsed Data:\n";
print_r($data);
