
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/general/Heading.vue';

const form = useForm({});

function loginAsGuest(): void {
    form.post(route('guest.login'));
}
</script>

<template>
    <Head title="Login" />
    <section class="login-section">
        <div class="login-container">
            <div class="login-card card">
                <Heading>Game Lobby</Heading>
                <div class="guest-login">
                    <h2>Join Instantly</h2>
                    <form @submit.prevent="loginAsGuest">
                        <button type="submit" class="guest-button primary">
                            Continue as Guest
                        </button>
                    </form>
                </div>
                <div class="separator">
                    <hr />
                    <span>OR</span>
                    <hr />
                </div>
                <div class="login-form">
                    <h2>Login with Account</h2>
                    <form @submit.prevent>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                id="email"
                                type="email"
                                placeholder="Full login coming soon"
                                readonly
                            />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input
                                id="password"
                                type="password"
                                readonly
                            />
                        </div>
                        <button class="login-button" type="submit" disabled>
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.login-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.login-container {
    width: 100%;
    display: flex;
    justify-content: center;
}

.login-card {
    width: 100%;
    max-width: 750px;
    display: flex;
    flex-direction: column;
    gap: 25px;
    padding: 30px 40px;
}

.guest-login h2, .login-form h2 {
    margin-bottom: 15px;
    font-size: 24px;
}

.guest-button {
    width: 100%;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
}

.separator {
    display: flex;
    align-items: center;
    gap: 15px;
}

.separator hr {
    flex-grow: 1;
    border: none;
    height: 1px;
    background-color: var(--secondary60);
}

.separator span {
    color: var(--text70);
    font-size: 14px;
}

.login-form form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.form-group label {
    font-size: 14px;
    color: var(--text80);
    padding-left: 5px;
}

.login-form input[readonly] {
    cursor: not-allowed;
    background: var(--secondary10);
}

.login-button {
    width: 100%;
    margin-top: 10px;
}
</style>


