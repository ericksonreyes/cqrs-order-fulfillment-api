<?php

namespace App\Http\Controllers\Helper;

class UserScopes
{
    /**
     * @var UserScope[]
     */
    private $scopes = [];

    /**
     * @param string $scopeString
     */
    public function addScope(string $scopeString): void
    {
        $this->scopes[] = new UserScope($scopeString);
    }

    /**
     * @return UserScope[]
     */
    public function scopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array $scopeArray
     */
    public function addFromArray(array $scopeArray): void
    {
        foreach ($scopeArray as $scopeString) {
            $this->addScope($scopeString);
        }
    }

    /**
     * @param $context
     * @param $model
     * @return bool
     */
    public function isAdmin($context, $model): bool
    {
        foreach ($this->scopes() as $scope) {
            if ($scope->context() === $context && $scope->model() === $model && $scope->isAdmin()) {
                return true;
            }
        }

        return false;
    }
}
