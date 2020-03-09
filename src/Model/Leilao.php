<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    private $finalizado;
    
    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }
    
    public function recebeLance(Lance $lance)
    {
        if(!empty($this->lances) && $this->ehDoUltimoUsuario($lance)){
           throw new \DomainException('Usuário não pode propor 2 lances seguidos');           
        }
        
        $totalLancesUser = $this->quantidadeLancesPorUsuario($lance->getUsuario());        
        
        if($totalLancesUser >=5 ){
           throw new \DomainException('Usuário não pode dar mais que 5 lances');
        }
        
        $this->lances[] = $lance;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function finalizado() : bool
    {
        return $this->finalizado;
    }
    
    /**
    * @return Lance[]
    */
    public function getLances(): array
    {
        return $this->lances;
    }
    
    public function quantidadeLancesPorUsuario(Usuario $usuario) : int
    {
        $totalLancesUsuario = array_reduce($this->lances, function($total, Lance $lanceAtual) use ($usuario){
            if($lanceAtual->getUsuario() == $usuario){
                return $total +1;
            }
            return $total;
        },
        0);
        
        return $totalLancesUsuario;
    }
    
    private function ehDoUltimoUsuario(Lance $lance) : bool
    {
        $ultimoLance = $this->lances[array_key_last($this->lances)];
        
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }
}
