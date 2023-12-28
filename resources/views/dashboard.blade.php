<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> --}}
    <div class="container p-3">
        <div class="row flex-wrap">
            @if (count($users) > 0)
                <div class="col-md-3">
                    <ul class="list-group gap-2">
                        @foreach ($users as $user)
                            <li class="list-group-item list-group-item-dark rounded d-flex flex-wrap gap-3 align-items-center cursor-pointer user-list"
                                data-id="{{ $user->id }}">
                                <img src="{{ $user->image }}" alt="{{ $user->name }} Image" class="img-thumbnail"
                                    width="50px">
                                <span>
                                    {{ $user->name }}
                                </span>
                                <span class="flex-1 d-flex justify-end">
                                    <span id="{{ $user->id }}-status" class="offline-status font-bold">
                                        Offline
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="{{ $user->id }}-status-icon-offline"
                                        class="offline-status" height="13" width="13" viewBox="0 0 512 512">
                                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512z" />
                                    </svg>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-9 py-2">
                    <h2 class="h2 text-center start-head">Click to start the chat</h2>
                    <div class="chat-section">
                        <div id="chat-container">
                            <!-- Chat appended here -->
                            {{-- Chat appended here --}}
                        </div>
                        <form action="" id="chat-form" class="d-flex align-items-center gap-2">
                            <input type="text" name="message" id="message" class="form-control"
                                placeholder="Enter your message">
                            <input type="submit" value="Send message" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            @else
                <div class="col-md-12">
                    <h6>
                        Users Not Found
                    </h6>
                </div>
            @endif
        </div>
    </div>
    <!-- Button trigger modal -->
    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteChatModel">
        Launch demo modal
    </button> --}}

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteChatModel" tabindex="-1" aria-labelledby="deleteChatModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered justify-content-center">
            <div class="modal-content" style='width:75%;'>
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteChatModelLabel">Delete Chat</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mx-auto">
                    <form action="" id="delete-chat-form">
                        @csrf
                        <input type="hidden" name="id" id="delete-chat-id">
                        <div class='d-flex flex-column gap-1 align-items-center text-center'>
                            <span>
                                <svg width='50' height='50' viewBox='0 0 60 60' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <circle cx='29.5' cy='30.5' r='26' stroke='#EA4335' stroke-width='3' />
                                    <path d='M41.2715 22.2715C42.248 21.2949 42.248 19.709 41.2715 18.7324C40.2949 17.7559 38.709 17.7559 37.7324 18.7324L29.5059 26.9668L21.2715 18.7402C20.2949 17.7637 18.709 17.7637 17.7324 18.7402C16.7559 19.7168 16.7559 21.3027 17.7324 22.2793L25.9668 30.5059L17.7402 38.7402C16.7637 39.7168 16.7637 41.3027 17.7402 42.2793C18.7168 43.2559 20.3027 43.2559 21.2793 42.2793L29.5059 34.0449L37.7402 42.2715C38.7168 43.248 40.3027 43.248 41.2793 42.2715C42.2559 41.2949 42.2559 39.709 41.2793 38.7324L33.0449 30.5059L41.2715 22.2715Z' fill='#EA4335' />
                                </svg>
                            </span>
                            <h2>Are you sure?</h2>
                            <p>
                                Will you delete the below message ? 
                            </p>
                            <p class="fs-5">
                                <q id="delete-message-q">A</q>
                            </p>
                            <div class='btns d-flex gap-3'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                <button type='submit' class='btn btn-danger' data-bs-dismiss='modal'>Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <!-- Update Modal -->
    <div class="modal fade" id="updateChatModel" tabindex="-1" aria-labelledby="updateChatModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered justify-content-center">
            <div class="modal-content" style='width:75%;'>
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="updateChatModelLabel">Update Chat</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mx-auto" style="width: 90%">
                    <form action="" id="update-chat-form">
                        @csrf
                        <div class='d-flex flex-column gap-3 align-items-center text-center'>
                            <input type="hidden" name="id" id="update-chat-id">
                            <input type="text" name="update-message" id="update-message" class="form-control" placeholder="Enter Message">
                            <div class='btns d-flex gap-3'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                <button type='submit' class='btn btn-primary' data-bs-dismiss='modal'>Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Update Modal -->
</x-app-layout>