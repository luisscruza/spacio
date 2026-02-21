<template>
    <h1>List of users</h1>
    <ul>
        @foreach ($users as $user)
            <li>{{ user.name }}</li>
        @endforeach
    </ul>
    <a href="/users/new">Create new user</a>
</template>
