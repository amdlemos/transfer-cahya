<x-layouts.guest>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="flex min-h-screen">
            <div class="flex-1 flex justify-center items-center">
                <div class="w-80 max-w-80 space-y-6">

                    <flux:heading class="text-center" size="xl">Bem vindo!</flux:heading>

                    <div class="flex flex-col gap-6">
                        <flux:input label="Email" type="email" name="email" placeholder="email@example.com" />

                        <flux:field>
                            <div class="mb-3 flex justify-between">
                                <flux:label>Password</flux:label>

                                <flux:link href="#" variant="subtle" class="text-sm">Esqueceu a senha?</flux:link>
                            </div>

                            <flux:input type="password" name="password" placeholder="Your password" />
                        </flux:field>

                        <!-- <flux:checkbox label="Remember me for 30 days" /> -->

                        <flux:button variant="primary" type="submit" class="w-full">Log in</flux:button>
                    </div>

                    <flux:subheading class="text-center">
                        Primeira vez por aqui? <flux:link href="/register">Registrar</flux:link>
                    </flux:subheading>
                </div>
            </div>

            <div class="flex-1 p-4 max-lg:hidden">
                <div
                    class="text-white relative rounded-lg h-full w-full bg-zinc-900 flex flex-col items-start justify-end p-16">
                    <div class="flex gap-2 mb-4">
                        <flux:icon.star variant="solid" />
                        <flux:icon.star variant="solid" />
                        <flux:icon.star variant="solid" />
                        <flux:icon.star variant="solid" />
                        <flux:icon.star variant="solid" />
                    </div>

                    <div class="mb-6 italic font-base text-3xl xl:text-4xl">
                        Transfer Cahya é a solução para transferências e compras entre parceiros.
                    </div>

                    <div class="flex gap-4">
                        <flux:avatar src="https://fluxui.dev/img/demo/caleb.png" size="xl" />

                        <div class="flex flex-col justify-center font-medium">
                            <div class="text-lg">Alan Lemos</div>
                            <div class="text-zinc-300">Criador Cahya</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>


</x-layouts.guest>
