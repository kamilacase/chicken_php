<?php

class Personnage
{
    private $_degats;
    private $_id;
    private $_nom;
    private $_xp;
    private $_lev;
    private $_life;
    
    const CEST_MOI = 1; // Constante renvoyée par la méthode `frapper` si on se frappe soi-même.
    const PERSONNAGE_TUE = 2; // Constante renvoyée par la méthode `frapper` si on a tué le personnage en le frappant.
    const PERSONNAGE_FRAPPE = 3; // Constante renvoyée par la méthode `frapper` si on a bien frappé le personnage.

    const DAMAGE_MIN = 5;
    const DAMAGE_MAX = 25;
    const LEVEL_THRESHOLD= 1000;

    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }
    
    public function frapper(Personnage $perso)
    {
        if ($perso->id() == $this->_id) {
            return self::CEST_MOI;
        }
        $this->_xp+=100;

        if ($this->_xp>=self::LEVEL_THRESHOLD){
            $this->_xp = 0;
            $this->_lev++;
        }
        // On indique au personnage qu'il doit recevoir des dégâts.
        // Puis on retourne la valeur renvoyée par la méthode : self::PERSONNAGE_TUE ou self::PERSONNAGE_FRAPPE
        return $perso->recevoirDegats($this);
    }
    
    public function hydrate(array $donnees)
    {
        
        foreach ($donnees as $key => $value) {
            $method = 'set'.ucfirst($key);
        
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    public function recevoirDegats($perso)
    {
        
        $degats= ceil(5 * ((1 + ($perso->lev() / 10))));
        $this->_degats += $degats;
        // Si on a 100 de dégâts ou plus, on dit que le personnage a été tué.
        if ($this->_degats >= 150) {
            return self::PERSONNAGE_TUE;
        }
    
        // Sinon, on se contente de dire que le personnage a bien été frappé.
        return self::PERSONNAGE_FRAPPE;
    }
    
    // GETTERS //
    
     public function life()
    {
        return $this->_life;
    }
    public function xp()
    {
        return $this->_xp;
    }
    public function lev()
    {
        return $this->_lev;
    }
    

    public function degats()
    {
        return $this->_degats;
    }
    
    public function id()
    {
        return $this->_id;
    }
    
    public function nom()
    {
        return $this->_nom;
    }
    ////////SETTERS///////////////////////////////////////////////////////////////////////////
    
    
    
    
    public function setLife($life)
    {
        $life = (int) $life;
        $this->_life = $life;
    }
    
    public function setDegats($degats)
    {
        $degats = (int) $degats;
    
        if ($degats >= 0 && $degats <= 100) {
            $this->_degats = $degats;
        }
    }

    public function setXp($xp)
    {

        $xp = (int) $xp;

        if ($xp >= 0 && $xp <=10000){
            $this->_xp = $xp;
        }
    }

    public function setLev($lev)
    {
        $lev = (int) $lev;

        if ($lev >= 0 && $lev <=100){
            $this->_lev = $lev;
        }
    }

    
    public function setId($id)
    {
        $id = (int) $id;
    
        if ($id > 0) {
            $this->_id = $id;
        }
    }
    
    public function setNom($nom)
    {
        if (is_string($nom)) {
            $this->_nom = $nom;
        }
    }

    public function nomValide()
    {
        return !empty($this->_nom);
    }
}
