<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Entity;

/**
 * @Entity
 * @Table(name="users")
 */
class User extends BaseEntity
{
	
	const GENDER_FEMALE	= true;
	const GENDER_MALE 	= false;
	
	const JSEDU_GRUPPENLEITER 	= "Gruppenleiter";
	const JSEDU_LAGERLEITER		= "Lagerleiter";
	const JSEDU_AUSBILDNER		= "Ausbildner";
	const JSEDU_EXPERTE			= "Experte";
	
	const PBSEDU_BASISKURS		= "Basiskurs";
	const PBSEDU_AUFBAUKURS		= "Aufbaukurs";
	const PBSEDU_PANOKURS		= "Panokurs";
	const PBSEDU_SPEKTRUM		= "Spektrum";
	const PBSEDU_TOPKURS		= "Topkurs";
	const PBSEDU_GILLWELL		= "Gillwell";
	
	public function __construct()
    {
		$this->userCamp  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->userGroup = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 * @var int
	 */
	private $id;
	
	/**
	 * @OneToOne(targetEntity="Name", cascade={"all"}, fetch="EAGER")
	 */
	private $name;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $scoutname;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $firstname;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $surname;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $street;
	
	/** @Column(type="string", length=16, nullable=true ) */
	private $zipcode;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $city;
	
	/** @Column(type="string", length=16, nullable=true ) */
	private $homeNr;
	
	/** @Column(type="string", length=16, nullable=true ) */
	private $mobilNr;
	
	/** @Column(type="date", nullable=true) */
	private $birthday;
	
	/** @Column(type="string", length=32, nullable=true ) */
	private $ahv;
	
	/** @Column(type="boolean", nullable=true) */
	private $gender;
	
	/** @Column(type="string", length=16, nullable=true ) */
	private $jsPersNr;
	
	/** @Column(type="string", length=16, nullable=true ) */
	private $jsEdu;
	
	/** @Column(type="string", length=16, nullable=true ) */
	private $pbsEdu;
	
	/** @Column(type="object", nullable=true ) */
	private $image;
	
	
	/**
	 * @var ArrayObject
	 * @OneToMany(targetEntity="UserCamp", mappedBy="user")
	 */
	private $userCamp;
	
	/**
	 * @var ArrayObject
	 * @OneToMany(targetEntity="UserGroup", mappedBy="user")
	 */
	private $userGroup;
	
	/**
	 * @OneToMany(targetEntity="UserRelationship", mappedBy="from")
	 */
	private $relationshipFrom;
	
	/**
	 * @OneToMany(targetEntity="UserRelationship", mappedBy="to")
	 */
	private $relationshipTo;
	

	public function getId(){	return $this->id;	}
	
	public function getName()
	{
		if( isset($this->name) )
			return $this->name->getName();
		else
			return null;
	}
	
	public function setName($name)
	{
		if( !isset($this->name) )
		{
			$this->name = new Name();
		}
		
		$this->name->setName($name);
		return $this;
	}
	
	public function getScoutname()            { return $this->scoutname; }
	public function setScoutname( $scoutname ){ $this->scoutname = $scoutname; return $this; }

	public function getFirstname()            {	return $this->firstname; }
	public function setFirstname( $firstname ){ $this->firstname = $firstname; return $this; }

	public function getSurname()          { return $this->surname; }
	public function setSurname( $surname ){ $this->surname = $surname; return $this; }
	
	public function getStreet()         { return $this->street; }
	public function setStreet( $street ){ $this->street = $street; return $this; }

	public function getZipcode()          { return $this->zipcode; }
	public function setZipcode( $zipcode ){ $this->zipcode = $zipcode; return $this; }

	public function getCity()       { return $this->city; }
	public function setCity( $city ){ $this->city = $city; return $this; }

	public function getHomeNr()         { return $this->homeNr; }
	public function setHomeNr( $homeNr ){ $this->homeNr = $homeNr; return $this; }

	public function getMobilNr()          { return $this->mobilNr; }
	public function setMobilNr( $mobilNr ){ $this->mobilNr = $mobilNr; return $this; }

	public function getBirthday()           { return $this->birthday; }
	public function setBirthday( $birthday ){ $this->birthday = $birthday; return $this; }

	public function getAHV()      { return $this->ahv; }
	public function setAHV( $ahv ){ $this->ahv = $ahv; return $this; }

	public function getGender()         { return $this->gender; }
	public function setGender( $gender ){ $this->gender = (BOOLEAN) $gender; return $this; }

	public function getJsPersNr()           { return $this->jsPersNr;	}
	public function setJsPersNr( $jsPersNr ){ $this->jsPersNr = $jsPersNr; return $this; }

	public function getJsEdu()        { return $this->jsEdu;	}
	public function setJsEdu( $jsEdu ){ $this->jsEdu = $jsEdu; return $this; }

	public function getPbsEdu()         { return $this->pbsEdu;	}
	public function setPbsEdu( $pbsEdu ){ $this->pbsEdu = $pbsEdu; return $this; }

	

	public function getDisplayName()
	{
		if( ! is_null( $this->scoutname ) )
		{	return $this->scoutname;	}
		
		return $this->firstname . " " . $this->surname;
	}

	public function isMale()
	{	return ( $this->gender == self::GENDER_MALE );		}
	
	public function isFemale()
	{	return ( $this->gender == self::GENDER_FEMALE );	}

	
	public function setImage( $image )
	{
		$this->image = $image;
		return $this;
	}

	public function getCamps()
	{
		return $this->userCamp;
	}
	
	public function getRelationshipFrom()
	{
		return $this->relationshipFrom;
	}
	
	public function getRelationshipTo()
	{
		return $this->relationshipTo;
	}
	
	private function isFriendTo($user)
	{
		$closure =  function($key, $element) use ($user){ 
			return $element->getType() == UserRelationship::TYPE_FRIEND && $element->getTo() == $user; 
		};
		
		return $this->getRelationshipFrom()->exists( $closure ); 
	}
	
	private function isFriendFrom($user)
	{
		$closure =  function($key, $element) use ($user){ 
			return $element->getType() == UserRelationship::TYPE_FRIEND  && $element->getFrom() == $user; 
		};
		
		return $this->getRelationshipTo()->exists( $closure ); 
	}
	
	/**
	 * True if friendship request has been sent but not yet accepted
	 */
	public function sentFriendshipRequestTo($user)
	{
		return $this->isFriendTo( $user ) && ! $this->isFriendFrom( $user ); 
	}
	
	/**
	 * True if friendship request has been received but not yet accepted
	 */
	public function receivedFriendshipRequestFrom($user)
	{
		return ! $this->isFriendTo( $user ) && $this->isFriendFrom( $user ); 
	}
	
	/**
	 * True for established friendships (both directions)
	 */
	public function isFriendOf($user)
	{
		return $this->isFriendTo( $user ) && $this->isFriendFrom( $user ); 
	}
	
}