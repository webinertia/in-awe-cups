<?php

declare(strict_types=1);

namespace User\Service;

use App\Model\ModelInterface;
use App\Model\ModelTrait;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ObjectPropertyHydrator as Hydrator;
use User\Db\UserGateway;
use User\Model\Roles;
use User\Service\UserServiceInterface;

use function array_key_exists;

#[\AllowDynamicProperties]
class UserService implements UserServiceInterface, ModelInterface
{
    use ModelTrait;

    /** @var array $acctData */
    public $acctData;
    /** @var array $socialMedia */
    public $socialMedia;
    /** @var array $memberDetails */
    public $memberDetails;
    /** @var int $id */
    public $id;
    /** @var string $userName */
    public $userName;
    /** @var string $email */
    public $email;
    /** @var string $password */
    public $password;
    /** @var string $role  */
    public $role;
    /** @var string $groupName */
    protected $groupName;
    /** @var string $firstName */
    public $firstName;
    /** @var string $lastName */
    public $lastName;
    /** @var string $profileImage */
    public $profileImage;
    /** @var string $age */
    public $age;
    /** @var string $birthday */
    public $birthday;
    /** @var UserGateway $gateway */
    protected $gateway;
    /** @var string $gender */
    public $gender;
    /** @var string $race */
    public $race;
    /** @var string $bio */
    public $bio;
    /** @var string companyName */
    public $companyName;
    /** @var string $jobTitle */
    public $jobTitle;
    /** @var string $mobileNumber */
    public $mobileNumber;
    /** @var string officeNumber */
    public $officeNumber;
    /** @var string $homeNumber */
    public $homeNumber;
    /** @var string $street */
    public $street;
    /** @var string $aptNumber */
    public $aptNumber;
    /** @var string $city */
    public $city;
    /** @var string $state */
    public $state;
    /** @var string $zip */
    public $zip;
    /** @var string $country */
    public $country;
    /** @var string $webUrl */
    public $webUrl;
    /** @var string $github */
    public $github;
    /** @var string $twitter */
    public $twitter;
    /** @var string $instagram */
    public $instagram;
    /** @var string $facebook */
    public $facebook;
    /** @var string $linkedin */
    public $linkedin;
    /** @var string $slack */
    public $slack;
    /** @var int $sessionLength */
    public $sessionLength;
    /** @var string regDate */
    public $regDate;
    /** @var int|bool $active */
    public $active;
    /** @var string $prefs_theme */
    public $prefsTheme;
    /** @var string $regHash */
    public $regHash;
    /** @var string resetTimeStamp */
    public $resetTimeStamp;
    /** @var string $resetHash */
    public $resetHash;
    /** @var int $ownerId */
    protected $ownerId;
    /** @var string $resourceId */
    protected $resourceId = 'users';
    /** @var array $logData */
    protected $logData = [];
    /** @var Roles $roleModel */
    protected $roles;
    /** @var bool $filterPassword */
    protected $filterPassword = true;
    /** @var Hydrator $hydrator */
    protected $hydrator;

    public function __construct(
        ?UserGateway $gateway = null
    ) {
        $this->hydrator = new Hydrator();
        if ($gateway instanceof UserGateway) {
            $this->gateway = $gateway;
        }
        $this->roles = new Roles();
    }

    public function getLogData(): array
    {
        return [
            'userId'       => $this->id,
            'userName'     => $this->userName,
            'role'         => $this->role,
            'firstName'    => $this->firstName,
            'lastName'     => $this->lastName,
            'email'        => $this->email,
            'profileImage' => $this->profileImage,
        ];
    }

    /**
     * @param array<mixed> $data
     */
    public function save(array $data, ?int $id = null): int
    {
        if ($id !== null) {
            $id = (int) $id;
        }
        /**
         * only returns a value other than zero if an insert or update was successful
         * which requires a change in data
         **/
        return $id === null
        ? $this->gateway->insert($data) : $this->gateway->update($data, ['id' => $id]);
    }

    public function exchangeArray(array $data)
    {
        $this->hydrator->hydrate($data, $this);
    }

    public function toArray(): array
    {
        if ($this->filterPassword) {
            unset($this->password);
        }
        return $this->hydrator->extract($this);
    }

    public function getArrayCopy(): array
    {
        return $this->toArray();
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function getFullName(): string
    {
        if ($this->firstName !== null && $this->lastName !== null) {
            return $this->firstName . ' ' . $this->lastName;
        } else {
            return $this->userName;
        }
    }

    public function getGroupName(): string
    {
        $this->groupName = $this->roles->getGroupName($this->role);
        return $this->groupName;
    }

    public function getOwnerId(): int
    {
        return (int) $this->id;
    }

    public function getRoleId(): string
    {
        return $this->role;
    }

    public function setResourceId(string $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setGateway(UserGateway $gateway): void
    {
        $this->gateway = $gateway;
    }

    public function setFilterPassword(bool $filterPassword): void
    {
        $this->filterPassword = $filterPassword;
    }

    public function getFilterPassword(): bool
    {
        return $this->filterPassword;
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->gateway->getAdapter();
    }

    public function getSectionColumns(?string $section = null): array
    {
        $map = [
            'member-details' => [
                'profileImage',
                'firstName',
                'lastName',
                'role',
                'jobTitle',
                'city',
                'state',
            ],
            'social-media'   => [
                'webUrl',
                'github',
                'twitter',
                'instagram',
                'facebook',
            ],
        ];
        if ($section !== null && array_key_exists($section, $map)) {
            return $map[$section];
        }
        return $map;
    }
}
