<?php
/**
concatenar diversos docuentos PDF
**/

require_once("../vendor/tcpdf/tcpdf.php");
require_once("../vendor/setasign/fpdi/fpdi.php");



class TestePdf extends FPDI{

    public  $angle = 0;
    public $files = array(
        'pdfs/pdf1/1.pdf',
        'pdfs/pdf2/2.pdf',
        'pdfs/pdf3/3.pdf',
        'pdfs/pdf4/4.pdf'
    );

    public function geraPDF(){


                /// interage com os pdfs
                $a = 1; // contador para marcação e adicao da pagina

                $lista="";

                foreach($this->files as $file){
                    ///
                    $PageCount = $this->setSourceFile($file);
                    //// interage com todas as páginas

                    for($i=1;$i<=$PageCount;$i++){

                        /// importa a pagina
                        /**
                         * Adiciona Capa a cada 4 arquivos
                         */
                        if($a==1 || $a%4==0) {

                            $this->addPage();

                            $this->SetXY(10, 20);
                            $this->SetFontSize(30);
                            $this->MultiCell(0, 0, "2 Cartorio de Resgistro de Documentos e Titulos e Pessoas Fisicas de Sao Paulo", 0, "C", false);

                            $this->SetXY(10, 100);
                            $this->SetFontSize(20);
                            $this->MultiCell(0, 0, "Central IRTD - \r\n Rua XV, n 40, Centro - Sao Paulo,SP", 0, "C", false);

                            $this->SetXY(10, 150);
                            $this->SetFontSize(20);
                            $this->MultiCell(0, 0, $lista, 0, "C", false);

                        }

                            $templateId = $this->importPage($i);
                            ///pega o tamanho da pafina importada
                            $tamanho = $this->getTemplateSize($templateId);
                            /// cria uma página landiscape or portrait dependendo da página importada

                            if ($tamanho['w'] > $tamanho['w']) {
                                $this->addPage('P', array($tamanho['h'], $tamanho['w']));
                            } else {
                                $this->addPage('P', array($tamanho['w'], $tamanho['h']));
                            }
                            ///usa a pagina importada
                            $this->useTemplate($templateId);
                            /// Adiciona marcações
                            ///adiciona o tipo do documento
                            $this->MarcaDocumento("DOC_".$a);
                            //// adiciona o numero da página do
                            // pdf
                            $this->MarcaDocumento("PAGINA ".$a,150);
                        $a++;
                    }
                }
                /// salva PDF
               $this->Output("E:/xampp/htdocs/teste_pdf/pdfs2.pdf","F");

    }

    function Rotate($angle,$x=-1,$y=-1) {

        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)

        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;

            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

    function _endpage()
    {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    function MarcaDocumento($txt,$y=100){
        $this->Rotate(90,204,$y);
        $this->SetFontSize(5);
        $this->SetFillColor(255,0,0);
        $this->Text(204,$y,$txt);
        $this->Rotate(0);
    }

    public function geraLista(){

        $l=$_SESSION['listagem'];
        $this->SetXY(10,150);
        $this->MultiCell(0,0,$l,1,"J",false);
    }



}

$pdf = new TestePdf();
$pdf->geraPDF();



