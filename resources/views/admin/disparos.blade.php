@extends('layouts.layout')

@section('title', 'Disparos')

@section('content')
    <h1 class="app-page-title">Disparos</h1>

    <div class="row g-4 mb-4">

        <div class="col-12 col-lg-12">
            <button class="btn btn-primary float-end text-white" onclick="createItem()"><i class="fab fa-whatsapp"></i> Novo disparo</button>
        </div>

        <div class="col-12 col-lg-12">
            <div class="app-card app-card-stat shadow-sm h-100">
                <div class="app-card-body p-3 p-lg-4">

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Tag</th>
                            <th scope="col">Pendentes</th>
                            <th scope="col">Enviadas</th>
                            <th scope="col">Ações</th>
                        </tr>
                        </thead>
                        <tbody>

                            @foreach ($disparos as $disparo)
                            <tr>
                                <th scope="row"> {{ $disparo->name }} </th>
                                <td> {{ $disparo->description }} </td>
                                <td> {{ $disparo->tag->name ? $disparo->tag->name : 'Sem tag' }} </td>
                                <td> {{ $disparo->qt_pending }} </td>
                                <td> {{ $disparo->qt_sent }} </td>
                                <td>
                                <a href="#" class="btn btn-primary text-white"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn btn-sm btn-danger text-white"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                       
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalItem" tabindex="-1" aria-labelledby="modalItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalItemLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row g-4 mb-4">

                    <div class="col-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="col-3">
                        <label for="description" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>

                    <div class="col-6">
                        <label for="tag_id" class="form-label">Tag de envio</label>
                        <select class="form-select" id="tag_id" name="tag_id" required>
                            <option value="text">Selecione</option>
                            {{-- <option value="text">Tag 1 | 22/04 | 22 envios </option> --}}
                            @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                    
                    <div class="col-9">
                        <label for="templates_id" class="form-label">Templates</label>
                        <select class="form-select" id="templates_id" name="templates_id[]" multiple required>
                            @foreach ($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary text-white">Salvar</button>
            </div>
            </div>
        </div>
    </div>

    @section('scripts')

    <script>

        const getItems = (id) => {

        fetch(`/disparos/${id}/show`)
        .then(response => response.json())
        .then(data => {
            
            const myModalAlternative = new bootstrap.Modal('#modalItem', {
                keyboard: false,
                backdrop: 'static'
            });

            document.getElementById('modalItemLabel').innerHTML = `Editar item ${data.name}`;

            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('tag_id').value = data.tag_id;
            document.getElementById('templates_id').value = data.templates_id;
            document.getElementById('status').value = data.status;

            myModalAlternative.show();

            document.querySelector('#modalItem .modal-footer button').setAttribute('onclick', `updateItem(${id})`);
            
            console.log(data);
        });

        }

        const createItem = () => {

        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('tag_id').value = '';
        document.getElementById('templates_id').value = '';
        document.getElementById('status').value = 'active';

        const myModal = new bootstrap.Modal('#modalItem', {
            keyboard: false,
            backdrop: 'static'
        });

        document.getElementById('modalItemLabel').innerHTML = 'Adicionar item';
        document.querySelector('#modalItem .modal-footer button').setAttribute('onclick', `saveItem()`);

        myModal.show();

        }

        const saveItem = async () => {

        let _token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const bodyData = JSON.stringify({
            _token: _token,
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            tag_id: document.getElementById('tag_id').value,
            templates_id: document.getElementById('templates_id').value,
            status: document.getElementById('status').value,
        });

        fetch('/disparos/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: bodyData
        })
        .then(response => response.json())
        .then(data => {
            
            if (data.error != 'true') {
                const myModal = bootstrap.Modal.getInstance(document.getElementById('modalItem'));
                myModal.hide();

                // location.reload();
            }

        })
        .catch((error) => {
            console.error('Error:', error);
        });

        }

        const updateItem = async (id) => {

        let _token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const bodyData = JSON.stringify({
            _token: _token,
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            tag_id: document.getElementById('tag_id').value,
            templates_id: document.getElementById('templates_id').value,
            status: document.getElementById('status').value,
        });

        fetch(`/disparos/${id}/update`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: bodyData
        })

        .then(response => response.json())

        .then(data => {
            
            if (data.error != 'true') {
                const myModal = bootstrap.Modal.getInstance(document.getElementById('modalItem'));
                myModal.hide();

                location.reload();
            }

        })
        .catch((error) => {
            console.error('Error:', error);
        });

        }

        const deleteItem = async (id) => {

        let _token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const bodyData = JSON.stringify({
            _token: _token
        });

        fetch(`/disparos/${id}/destroy`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: bodyData
        })

        .then(response => response.json())

        .then(data => {
            
            if (data.error != 'true') {
                location.reload();
            }

        })
        .catch((error) => {
            console.error('Error:', error);
        });

        }

    </script>

    @endsection

@endsection