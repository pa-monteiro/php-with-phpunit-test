<?php

namespace Alura\Leilao\Tests;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Model\Avaliador;


class AvaliadorTest extends TestCase
{
    /** @var Avaliador */
    private $leiloeiro;

    /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatória
     */
    
    public function testAvaliadorDeveEncontrarOMaiorValor(Leilao $leilao)
    {        
        // Act - When
        $this->leiloeiro->avalia($leilao);
        
        $maiorValor = $this->leiloeiro->getMaiorValor();
        
        // Assert - Then
        $valorEsperado = 3000;

        self::assertEquals(2500, $maiorValor);

    }


     /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatória
     */

    public function testAvaliadorDeveEncontrarOMenorValor(Leilao $leilao)
    {
        // Act - When
        $this->leiloeiro->avalia($leilao);
        
        $menorValor = $this->leiloeiro->getMenorValor();

        self::assertEquals(1700, $menorValor);

    }

     /**
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemAleatória
     */

    public function testAvaliadorDeveBuscarOsTresMaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        
        $maioresLances = $this->leiloeiro->getMaioresLances();

        self::assertCount(3, $maioresLances);
        self::assertEquals(2500, $maioresLances[0]->getValor());
        self::assertEquals(2000, $maioresLances[1]->getValor());
        self::assertEquals(1700, $maioresLances[2]->getValor());

    }

    public function testLeilaoVazioNaoDeveSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possivel avaliar leilão vazio');
        $leilao = new Leilao('Fusca Azul');
        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');
    
        $leilao = new Leilao('Fiat 147 0KM');

        $leilao->recebeLance(new Lance(new Usuario('Teste'), 2000));

        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);

    }

    // ------------------- DADOS 

    public function leilaoEmOrdemCrescente()
    {
        $leilao = new Leilao('Fiat 147 0KM');
        
        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        
        $leilao->recebeLance(new Lance($joao, 1700));
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($ana, 2500));
        
        return [
           'ordem-crescente' => [$leilao]
        ];
    }

    public function leilaoEmOrdemDecrescente()
    {
        $leilao = new Leilao('Fiat 147 0KM');
        
        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        
        $leilao->recebeLance(new Lance($ana, 2500));
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($joao, 1700));
        
        return [
            'ordem-decrescente' => [$leilao]
        ];
    }

    public function leilaoEmOrdemAleatória()
    {
        $leilao = new Leilao('Fiat 147 0KM');
        
        $maria = new Usuario('Maria');
        $joao = new Usuario('João');
        $ana = new Usuario('Ana');
        
        $leilao->recebeLance(new Lance($maria, 2000));
        $leilao->recebeLance(new Lance($ana, 2500));
        $leilao->recebeLance(new Lance($joao, 1700));
        
        return [
            'ordem-aleatoria' =>  [$leilao]
        ];
    }

    protected function setUp() : void
    {
        $this->leiloeiro = new Avaliador();
    }
}