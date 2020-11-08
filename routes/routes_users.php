<?php


/**
 * HTTP Auth - Minimalist authentication to return a JWT
 */
$router

    /**
    * Get usersGet
    * Summary: Create a new user in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/users', function () use ($router) {
        $qb = new QueryBuilder();
        $r = $qb
            ->table('users')
            ->fields(['name', 'email'])
            ->select();

        return $r;
    })

    /**
    * GET usersUsernameGet
    * Summary: Get a user from  the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('GET', '/users/(\w+)', function ($username) use ($router) {
        $qb = new QueryBuilder();
        $token = $qb
            ->table('tokens')
            ->fields(['id'])
            ->where(["username = '$username'"])
            ->selectOne();
        $token_id = $token->values[0]->id;
        $user = $qb
            ->table('users')
            ->fields(['name', 'email'])
            ->where(["tokens_id = $token_id"])
            ->selectOne();

        return $user;
    })
    
    /**
    * POST usersUsernamePost
    * Summary: Create a new user in the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('POST', '/users/(\w+)', function ($username) use ($router) {
        if (strpos($username, 'DEVICE_') === 0)
            throw new HttpException(400, 'Username started with DEVICE_ is not allowed');
        
        $qb = new QueryBuilder();
        // Checking if we have the username registered
        $token = $qb
            ->table('tokens')
            ->fields(['id'])
            ->where(["username = '$username'"])
            ->select();
        if (count($token->values) > 0)
            throw new HttpException(409, "Duplicated username: $username");
        
        $data = $router->body;

        $role_uid = $data->role;
        $role = $qb
            ->table('roles')
            ->fields(['id'])
            ->where(["uid = '$role_uid'"])
            ->selectOne();
        if (count($role->values) == 0)
            throw new HttpException(404, "Role not found: $role_uid");
        $role_id = $role->values[0]->id;

        // JWT token secret
        $token_id = $qb
            ->table('tokens')
            ->fields(['username', 'secret', 'password', 'roles_id'])
            ->insert([
                $username,
                base64_encode(random_bytes(10)), 
                password_hash($data->password, PASSWORD_DEFAULT),
                $role_id]);

        // Device UID is unique, lets continue
        $user_id = $qb
            ->table('users')
            ->fields(['name', 'email', 'tokens_id'])
            ->insert([$data->name, $data->email, $token_id]);
        $user = $qb
            ->table('users')
            ->fields(['name', 'email'])
            ->where(["id = $user_id"])
            ->selectOne();
        return $user;

    })

    /**
    * PUT usersUsernamePut
    * Summary: Update a user from the system
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('PUT', '/users/(\w+)', function ($username) use ($router) {
        $qb = new QueryBuilder();
        // Checking if we have the username registered
        $token = $qb
            ->table('tokens')
            ->fields(['id'])
            ->where(["username = '$username'"])
            ->selectOne();
        if (count($token->values) == 0)
            throw new HttpException(404, "Username not in the system: $username");

        $data = $router->body;
        $token_id = $token->values[0]->id;

        $r = $qb
            ->table('users')
            ->fields(['name', 'email'])
            ->where(["tokens_id = $token_id"])
            ->update([$data->name, $data->email]);
        
        $user = $qb
            ->table('users')
            ->fields(['name', 'email'])
            ->where(["tokens_id = $token_id"])
            ->selectOne();
        return $user;

    })
    
    /**
    * PUT usersUsernamePasswordPut
    * Summary: Update users password
    * Notes: 
    * Output-Formats: [application/json]
    */
    ->on('PUT', '/users/(\w+)/password', function ($username) use ($router) {
        $qb = new QueryBuilder();
        // Looking for the username in the database
        $token = $qb
            ->table('tokens')
            ->fields(['id', 'password'])
            ->where(["username = '$username'"])
            ->selectOne();
        if (count($token->values) == 0)
            throw new HttpException(404, "Username not in the system: $username");
        
        $data = $router->body;
        // Username not found or current password doesn't match
        if ($token == NULL || !password_verify($data->current_password, $token->values[0]->password))
            throw new HttpException(401, 'Username or password does not match');
            
        $token_id = $token->values[0]->id;
        $r = $qb
            ->table('tokens')
            ->fields(['password'])
            ->where(["id = $token_id"])
            ->update([password_hash($data->new_password, PASSWORD_DEFAULT)]);

        return array("Ok");
    })
    ;