<?php

    echo 'hola ';

//
//
//class PDF extends FPDF
//{
//    // Cabecera de página
//    function Header()
//    {
//        $this->SetFillColor(0,0,0);
//        $this->Rect(0,0,150,20,'F');
//        // Logo
//        $this->Image('assets/images/logo.png',60,8,0,0,'PNG','www.dumbu.pro');
//        // Salto de línea
//        $this->Ln(20);
//    }
//
//    // Pie de página
//    function Footer()
//    {
//        // Posición: a 2 cm del final
//        $this->SetY(-20);
//        // Arial bold 10
//        $this->SetFont('Arial','B',10);
//        $this->SetTextColor(128,128,128);
//        $this->Cell(0,10,'www.dumbu.pro',0,1,'C',false,'www.dumbu.pro');
//        // Arial italic 8
//        $this->SetFont('Arial','I',8);
//        $this->SetTextColor(0,0,0);
//        // Número de página
//        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');        
//    }
//}
//
//// Creación del objeto de la clase heredada
//$pdf = new PDF('P','mm','A5');
//$pdf->AliasNbPages();
//$pdf->AddPage();
//$pdf->Image('assets/images/sim.png',65);
//// Arial bold 15
//$pdf->SetFont('Arial','B',16);
//$pdf->SetTextColor(0,128,0);
//// Título
//$pdf->Cell(0,20,'Pagamento aprovado!',0,1,'C');
//$pdf->SetFont('Times','',12);
//$pdf->SetTextColor(0,0,0);
//$pdf->Cell(0,10,'Seu pagamento para a conta @nome_do_usuario',0,1,'C');
//$pdf->Cell(0,0,'foi feito com sucesso. :)',0,1,'C');
//$pdf->Ln(10);
//$pdf->SetFont('Times','B',12);
//$pdf->Cell(0,10,utf8_decode('Dados da cobrança:'),0,1,'C');
//$pdf->SetFont('Times','',12);
//$pdf->Cell(0,10,utf8_decode('JOSE R OLIVEIRA - CARTÃO ****3598'),0,1,'C');
//$pdf->Cell(0,10,'fulano@gmail.com',0,1,'C');
//$pdf->Ln(10);
//$pdf->Cell(0,10,'25/11/2017 - R$ 79,90 - (Vel. Moderada) - Trans.#9632',1,1,'C');
//$pdf->Ln(30);
//$pdf->Cell(0,10,utf8_decode('Se tiver dúvidas ou precisar de ajuda é só nos escrever:'),0,1,'C');
//$pdf->Cell(0,0,'atendimento@dumbu.pro',0,1,'C');
//$pdf->Ln(10);
//$pdf->Output('I','invoice.pdf');$pdf->SetDisplayMode(75);

?>