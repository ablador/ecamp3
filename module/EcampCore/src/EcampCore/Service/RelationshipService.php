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

namespace EcampCore\Service;

use EcampCore\Acl\DefaultAcl;

use EcampCore\Entity\User;
use EcampCore\Entity\Camp;
use EcampCore\Entity\UserCamp;
use EcampCore\Entity\UserRelationship;


class RelationshipService extends ServiceBase
{
	/**
	 * @return EcampCore\Service\UserService
	 */
	private function getUserService(){
		$this->locateService('ecamp.service.user');
	}
	
	/**
	 * @return EcampCore\Repository\UserRepository
	 */
	private function getUserRepo(){
		return $this->locateService('ecamp.repo.user');
	}

	/**
	 * @return EcampCore\Repository\UserRelationshipRepository
	 */
	private function getUserRelationshipRepo(){
		return $this->locateService('ecamp.repo.userrelationship');
	}
	
	
	public function _setupAcl(){
		
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'GetFriends');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'GetRequests');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'GetInvitations');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'RequestRelationship');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'DeleteRequest');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'AcceptInvitation');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'RejectInvitation');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'CancelRelationship');
		
	}
	
	
	public function Get($id, $user_id = null){
		if($user_id == null){
			if($id instanceof \CoreApi\Entity\UserRelationship){
				return $id;
			}
			
			return $this->getUserRelationshipRepo()->find($id);
		}
		else{
			$user1 = $this->getUserService()->Get($id);
			$user2 = $this->getUserService()->Get($user_id);
			
			return $this->getUserRelationshipRepo()->findByUsers($user1, $user2);
		}
	}
	
	
	/**
	 * @return Doctrine\Common\Collection\ArrayCollection
	 */
	public function GetFriends(){
		$user = $this->getUserService()->Get();
		return $this->getUserRepo()->findFriends($user);
	}
	
	
	/**
	 * @return Doctrine\Common\Collection\ArrayCollection
	 */
	public function GetRequests(){
		$user = $this->getUserService()->Get();
		return $this->getUserRepo()->findFriendRequests($user);
	}
	
	
	/**
	 * @return Doctrine\Common\Collection\ArrayCollection
	 */
	public function GetInvitations(){
		$user = $this->getUserService()->Get();
		return $this->getUserRepo()->findFriendInvitations($user);
	}
	
	
	/**
	 * @param User $toUser
	 * @return UserRelationship
	 */
	public function RequestRelationship(User $toUser){
		$user = $this->getUserService()->Get();
		$ur = $this->getUserRelationshipRepo()->findByUsers($user, $toUser);
		
		$this->validationAssert(
			$ur == null, 
			"There is already a relationship between these users");
		
		$ur = new UserRelationship($user, $toUser);
		$this->persist($ur);
		
		return $ur;
	}
	
	
	/**
	 * @param User $toUser
	 */
	public function DeleteRequest(User $toUser){
		$user = $this->getUserService()->Get();
		$ur = $this->getUserRelationshipRepo()->findByUsers($user, $toUser);
		
		$this->validationAssert(
			$ur != null && $ur->getCounterpart() == null,
			"There is no open request to delete");
		
		if($ur){
			$this->remove($ur);
		}
	}
	
	
	/**
	 * @param User $fromUser
	 * @return UserRelationship
	 */
	public function AcceptInvitation(User $fromUser){
		$user = $this->getUserService()->Get();
		$ur = $this->getUserRelationshipRepo()->findByUsers($fromUser, $user);
		
		$this->validationAssert(
			$ur && $ur->getCounterpart() == null,
			"There is no open invitation to accept");
		
		$cp = new UserRelationship($user, $fromUser);
		$this->persist($cp);
		
		UserRelationship::Link($ur, $cp);
		
		return $cp;
	}
	
	
	/**
	 * @param User $fromUser
	 */
	public function RejectInvitation(User $fromUser){
		$user = $this->getUserService()->Get();
		$ur = $this->getUserRelationshipRepo()->findByUsers($fromUser, $user);
		
		$this->validationAssert(
			$ur && $ur->getCounterpart() == null,
			"There is no open invitation to delete");
		
		if($ur){
			$this->remove($ur);
		}
	}
	
	
	public function CancelRelationship(User $withUser){
		$user = $this->getUserService()->Get();
		$ur = $this->getUserRelationshipRepo()->findByUsers($user, $withUser);
		
		$this->validationAssert(
			$ur && $ur->getCounterpart(),
			"There is no relationship to be canceled");
		
		if($ur && $ur->getCounterpart()){
			$this->remove($ur->getCounterpart());
			$this->remove($ur);
		}
	}
	
}