<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />

        <div class="flex flex-col justify-center items-center min-h-[80vh] bg-gray-50 py-12 sm:px-6 lg:px-8">
            
            <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Acesse sua conta
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Ou <Link :href="route('register')" class="font-medium text-indigo-600 hover:text-indigo-500">crie uma nova conta grátis</Link>
                </p>
            </div>

            <div class="w-full sm:max-w-md bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">
                
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email" />
                        <div class="mt-1">
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Senha" />
                        <div class="mt-1">
                            <TextInput
                                id="password"
                                type="password"
                                class="block w-full"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
                        </label>

                        <div class="text-sm">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="font-medium text-indigo-600 hover:text-indigo-500"
                            >
                                Esqueceu a senha?
                            </Link>
                        </div>
                    </div>

                    <div>
                        <PrimaryButton class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            ENTRAR
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>