{{-- deactivate --}}
<div class="modal fade" id="deactivate-user-{{ $user->id }}">
    <div class="modal-dailog">
        <div class="momdal-content border-danger">
            <div class="modal-content border-danger">
                <div class="modal-header borde-rdanger">
                    <h3 class="modal-title text-danger">
                    <i class="fas fa-user-uslush"></i> Deactivate User`
                    </h3>
                </div>
                <div class="modal-body">
                    Are you sure you want to deactivate <span class="fw-bold">{{ $user->name }}</span>
                </div>
                <div class="modal-footer border-0">
                    <form action="#" method="post">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outline danger btn-sm" data-bs-toggle="modal" type="button">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">Deactivate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>