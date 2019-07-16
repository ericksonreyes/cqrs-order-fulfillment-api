<?php

namespace App\Http\Controllers\Helper;

/**
 * Class UserScope
 * @package App\Http\Controllers
 */
class UserScope
{

    /**
     * @var string
     */
    private $context;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string[]
     */
    private $allowedActions = [];

    /**
     * UserScope constructor.
     * @param string $userScopeString
     */
    public function __construct(string $userScopeString)
    {
        $userScopeArray = explode(':', $userScopeString);
        $this->context = $userScopeArray[0] ?? '';
        $this->model = $userScopeArray[1] ?? '';
        $this->userType = $userScopeArray[2] ?? '';

        $actions = isset($userScopeArray[3]) ? explode(',', $userScopeArray[3]) : [];
        foreach ($actions as $action) {
            $this->allowedActions[] = $action;
        }
    }

    /**
     * @param string $userScope
     * @return UserScope
     */
    public static function make(string $userScope): UserScope
    {
        return new static($userScope);
    }

    /**
     * @return string
     */
    public function context(): string
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function model(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function userType(): string
    {
        return $this->userType;
    }

    /**
     * @return string[]
     */
    public function allowedActions(): array
    {
        return $this->allowedActions;
    }

    /**
     * @param string $action
     * @return bool
     */
    public function isAllowedTo(string $action): bool
    {
        return in_array($action, $this->allowedActions(), true);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        $isAdminUserType = $this->userType() === 'admin';
        $hasAdminAction = $this->isAllowedTo('admin');
        return $isAdminUserType || $hasAdminAction;
    }
}
