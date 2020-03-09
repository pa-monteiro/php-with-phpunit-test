<?php

namespace Alura\Leilao\Tests\Model;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Lance;

class LeilaoTest extends TestCase
{

    /**
     * @dataProvider geraLances
     */

    public function testLeilaoDeveReceberLances(int $qtdLances, Leilao $leilao, array $valores)
    {       
        self::assertCount($qtdLances, $leilao->getLances());

        foreach($valores as $i => $v){
            self::assertEquals($v, $leilao->getLances()[$i]->getValor());
        }
    }

    public function geraLances()
    {
        $joao = new Usuario('joao');
        $maria = new Usuario('maria');

        $leilaoCom1Lance = new Leilao('Fusca 1972 0KM');        
        $leilaoCom1Lance->recebeLance(new Lance($maria, 5000));

        $leilaoCom2Lances = new Leilao('Fiat 147 0KM');        
        $leilaoCom2Lances->recebeLance(new Lance($joao, 1000));
        $leilaoCom2Lances->recebeLance(new Lance($maria, 2000));

        return [
            '2-lances' => [2, $leilaoCom2Lances, [1000, 2000]],
            '1-lance' => [1, $leilaoCom1Lance, [5000]]
        ];
    }

    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 2 lances seguidos');

        $leilao = new Leilao('Variante');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));

        self::assertCount(1, $leilao->getLances());
        self::assertEquals(1000, $leilao->getLances()[0]->getValor());
        

    }  

    public function testLeilaoNaoDeveAceitarMaisDeCincoLancesDeUmUnicoUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode dar mais que 5 lances');
        $leilao = new Leilao('Brasilia Amarela');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1200));        
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2400));        
        $leilao->recebeLance(new Lance($joao, 3000));
        $leilao->recebeLance(new Lance($maria, 3600));        
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($maria, 4800));        
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));        


        $leilao->recebeLance(new Lance($joao, 6000));

        self::assertCount(10, $leilao->getLances());
        self::assertEquals(5500, $leilao->getLances()[array_key_last($leilao->getLances())]->getValor());


    }

    public function testUsuarioNaoPodeProporDoisLancesSeguidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 2 lances seguidos');
        $leilao = new Leilao('Variante');
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($maria, 500));
        $leilao->recebeLance(new Lance($joao,1000));
        $leilao->recebeLance(new Lance($joao,1200));


    }

    

}