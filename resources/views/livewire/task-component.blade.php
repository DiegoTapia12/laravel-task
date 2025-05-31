<div wire:poll='renderAllTasks'>
    <a class="inline-flex items-center gap-2 rounded-md border bg-blue-600 px-8 py-2 text-white hover:bg-blue-500 mb-4"
        wire:click='openModalTask' href="#">
        <span class="text-sm font-medium"> Nuevo </span>
    </a>

    {{-- table --}}
    <div class="overflow-x-auto rounded border border-gray-300 shadow-sm">
        <table class="min-w-full divide-y-2 divide-gray-200">
            <thead class="ltr:text-left rtl:text-right">
                <tr class="*:font-medium *:text-gray-900">
                    <th class="px-3 py-2 whitespace-nowrap">Titulo</th>
                    <th class="px-3 py-2 whitespace-nowrap">Descripcion</th>
                    <th class="px-3 py-2 whitespace-nowrap">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 text-center">
                @foreach ($tasks as $task)
                <tr class="*:text-gray-900 *:first:font-medium">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $task->titulo }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $task->descripcion }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        @if ( (isset($task->pivot) && $task->pivot->permission == 'edit') || (auth()->user()->id ==
                        $task->user_id))
                        <a wire:click='openModalTask({{ $task }})'
                            class="inline-flex items-center gap-2 rounded-md border border-indigo-600 bg-indigo-600 px-4 py-2 text-white hover:bg-transparent hover:text-indigo-600 focus:ring-3 focus:outline-hidden"
                            href="#">
                            <span class="text-sm font-medium"> Editar </span>
                        </a>
                        <a wire:click='openModalShare'
                            class="inline-flex items-center gap-2 rounded-md border border-yellow-600 bg-yellow-600 px-4 py-2 text-white hover:bg-transparent hover:text-yellow-600 focus:ring-3 focus:outline-hidden"
                            href="#">
                            <span class="text-sm font-medium"> Compartir </span>
                        </a>
                        <a wire:click='deletTask({{ $task->id }})'
                            wire:confirm="Are you sure you want to delete this post?"
                            class="inline-flex items-center gap-2 rounded-md border border-red-600 bg-red-600 px-4 py-2 text-white hover:bg-transparent hover:text-red-600 focus:ring-3 focus:outline-hidden"
                            href="#">
                            <span class="text-sm font-medium"> Eliminar </span>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- end table --}}

    {{-- modal create y update --}}
    @if ($modal)
    <div class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4" role="dialog" aria-modal="true"
        aria-labelledby="modalTitle">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
            <h2 id="modalTitle" class="text-xl font-bold text-gray-900 sm:text-2xl">Crear nueva tarea
            </h2>

            <div class="mt-4">
                <form action="">
                    <label for="titulo">
                        <span class="text-sm font-medium text-gray-700"> Titulo </span>

                        <input type="text" id="titulo" name="titulo" wire:model='titulo'
                            class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm mb-4" />
                    </label>

                    <label for="descripcion">
                        <span class="text-sm font-medium text-gray-700"> Descripcion </span>

                        <input type="email" id="descripcion" name="descripcion" wire:model='descripcion'
                            class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm" />
                    </label>
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-2">
                <button type="button" wire:click='createUpdateTask'
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                    {{ isset($uniqueTask->id) ? 'Actualizar tarea' : 'Crear tarea' }}
                </button>

                <button type="button" wire:click='closeModalTask'
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700">
                    Cancelar
                </button>
            </footer>
        </div>
    </div>
    @endif
    {{-- end modal --}}

    {{-- modal compartir --}}
    @if ($modal2)
    <div class="fixed inset-0 z-50 grid place-content-center bg-black/50 p-4" role="dialog" aria-modal="true"
        aria-labelledby="modalTitle">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
            <h2 id="modalTitle" class="text-xl font-bold text-gray-900 sm:text-2xl">Compartir tarea
            </h2>

            <div class="mt-4">
                <form action="">
                    <label for="titulo">
                        <span class="text-sm font-medium text-gray-700"> Usuario </span>

                        <div>
                            <label for="Headline">
                                <span class="text-sm font-medium text-gray-700"> Headliner </span>

                                <select name="Headline" id="Headline"
                                    class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm">
                                    <option value="">Seleccione un usuario</option>
                                    @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </label>

                    <label for="descripcion">
                        <span class="text-sm font-medium text-gray-700"> Permisos </span>

                        <select name="Headline" id="Headline"
                            class="mt-0.5 w-full rounded border-gray-300 shadow-sm sm:text-sm">
                            <option value="">Seleccione un permiso</option>
                            <option value="edit">Editar</option>
                            <option value="view">Ver</option>
                        </select>
                    </label>
                </form>
            </div>

            <footer class="mt-6 flex justify-end gap-2">
                <button type="button" wire:click='shareTask({{ $task }})'
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                    compartir
                </button>

                <button type="button" wire:click='closeModalShare'
                    class="rounded bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700">
                    Cancelar
                </button>
            </footer>
        </div>
    </div>
    @endif
    {{-- end modal --}}
</div>