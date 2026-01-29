<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar Conta" />

        <div class="flex flex-col justify-center items-center min-h-[80vh] bg-gray-50 py-12 sm:px-6 lg:px-8">
            
            <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
                <h2 class="text-3xl font-extrabold text-gray-900">
                    Crie sua conta
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Já tem cadastro? <Link :href="route('login')" class="font-medium text-indigo-600 hover:text-indigo-500">Faça login aqui</Link>
                </p>
            </div>

            <div class="w-full sm:max-w-md bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">
                
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <InputLabel for="name" value="Nome Completo" />
                        <div class="mt-1">
                            <TextInput
                                id="name"
                                type="text"
                                class="block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <div class="mt-1">
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full"
                                v-model="form.email"
                                required
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
                                autocomplete="new-password"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirmar Senha" />
                        <div class="mt-1">
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="block w-full"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div>
                        <PrimaryButton class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            CRIAR CONTA
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>