@if(isset($showInfoVerificationModal) && $showInfoVerificationModal)
@php $missing = $missingFields ?? []; @endphp

<div class="modal fade" id="infoVerificationModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:16px;border:none">
      <div class="modal-body p-4">
        
        {{-- Header --}}
        <div class="d-flex align-items-start gap-3 mb-3">
          <div style="width:44px;height:44px;background:#FBEAF0;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-user-check" style="color:#C2185B;font-size:18px"></i>
          </div>
          <div>
            <h5 class="mb-1" style="font-size:16px;font-weight:600">Vérification de vos informations</h5>
            <p class="mb-0 text-muted" style="font-size:13px">
              Veuillez vérifier votre <b>Nom et Prénom</b> exactement comme ils sont écrits sur votre CIN pour l'impression de votre diplôme.
            </p>
          </div>
        </div>

        {{-- Alert champs manquants --}}
        @if(count($missing) > 0)
        <div class="alert d-flex align-items-start gap-2 mb-3" style="background:#FAEEDA;border:1px solid #FAC775;border-radius:10px;font-size:13px;color:#633806">
          <i class="fas fa-exclamation-circle mt-1" style="color:#854F0B"></i>
          <div>
            <strong>Champs à compléter :</strong>
            @foreach($missing as $field)
              <span class="badge" style="background:#F7C1C1;color:#501313;margin:2px">
                {{ $field }}
              </span>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Progress --}}
        <div class="mb-3">
          <small class="text-muted" id="stepLabel">Étape 1 sur 2 — Identité officielle</small>
          <div class="progress mt-1" style="height:4px;border-radius:2px">
            <div class="progress-bar" id="progressBar" style="width:50%;background:#C2185B"></div>
          </div>
        </div>

        <form id="infoVerifForm">
          @csrf
          
          {{-- Étape 1: Nom, Prénom, CIN, Date Naissance --}}
          <div id="verif-step1">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600">Nom (Français) <span class="text-danger">*</span></label>
                <input type="text" name="nom" class="form-control @if(in_array('nom', $missing)) border-danger @endif" 
                       value="{{ auth()->user()->nom ?? auth()->user()->name }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600">Prénom (Français) <span class="text-danger">*</span></label>
                <input type="text" name="prenom" class="form-control @if(in_array('prenom', $missing)) border-danger @endif" 
                       value="{{ auth()->user()->prenom }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600">
                  CIN @if(in_array('cin', $missing))<span style="color:#E24B4A"> *</span>@endif
                </label>
                <input type="text" name="cin" class="form-control @if(in_array('cin', $missing)) border-danger @endif"
                  value="{{ auth()->user()->cin }}" placeholder="Ex: AB123456" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600">
                  Date de naissance @if(in_array('birth_date', $missing))<span style="color:#E24B4A"> *</span>@endif
                </label>
                <input type="date" name="birth_date" class="form-control @if(in_array('birth_date', $missing)) border-danger @endif"
                  value="{{ auth()->user()->birth_date?->format('Y-m-d') }}" required>
              </div>
              <div class="col-md-12">
                <label class="form-label" style="font-size:12px;font-weight:600">
                  Lieu de naissance @if(in_array('lieu_naissance', $missing))<span style="color:#E24B4A"> *</span>@endif
                </label>
                <input type="text" name="lieu_naissance" class="form-control @if(in_array('lieu_naissance', $missing)) border-danger @endif"
                  value="{{ auth()->user()->lieu_naissance }}" placeholder="Ex: Casablanca" required>
              </div>
            </div>
          </div>

          {{-- Étape 2: Contacts et Nationalité --}}
          <div id="verif-step2" style="display:none">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600">Nationalité</label>
                <input type="text" name="nationalite" class="form-control" value="{{ auth()->user()->nationalite ?? 'Marocaine' }}">
              </div>
              <div class="col-md-6">
                <label class="form-label" style="font-size:12px;font-weight:600">Téléphone</label>
                <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
              </div>
              <div class="col-12">
                <label class="form-label" style="font-size:12px;font-weight:600">Adresse</label>
                <input type="text" name="address" class="form-control" value="{{ auth()->user()->address }}">
              </div>
              <div class="col-12 text-center py-2">
                 <div class="p-3 rounded-3" style="background:#FFF9F0; border: 1px dashed #FFD580; font-size:12px; color:#966917">
                    <i class="fas fa-info-circle me-1"></i> Ces informations seront utilisées pour générer vos attestations et diplômes. Toute erreur de saisie est sous votre responsabilité.
                 </div>
              </div>
            </div>
          </div>
        </form>

      </div>
      <div class="modal-footer border-0 pt-0 px-4 pb-4 justify-content-between">
        <button type="button" class="btn btn-link btn-sm text-muted text-decoration-none" id="btnSkip" onclick="skipVerif()">
          Plus tard
        </button>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light btn-sm" id="btnPrev" onclick="prevVerifStep()" style="display:none">Retour</button>
            <button type="button" class="btn btn-sm text-white px-4" id="btnNext" onclick="nextVerifStep()" style="background:#C2185B">
              Continuer →
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let verifStep = 1;

document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('infoVerificationModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static', keyboard: false
        });
        modal.show();
    }
});

function nextVerifStep() {
    if (verifStep === 1) {
        // Validation simple du Step 1
        const required = ['nom', 'prenom', 'cin', 'birth_date', 'lieu_naissance'];
        let valid = true;
        required.forEach(f => {
            const el = document.querySelector(`[name="${f}"]`);
            if (el && !el.value.trim()) {
                el.classList.add('border-danger');
                valid = false;
            } else if (el) {
                el.classList.remove('border-danger');
            }
        });

        if (!valid) return;

        document.getElementById('verif-step1').style.display = 'none';
        document.getElementById('verif-step2').style.display = 'block';
        document.getElementById('btnPrev').style.display = 'block';
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('stepLabel').textContent = 'Étape 2 sur 2 — Coordonnées & Nationalité';
        document.getElementById('btnNext').textContent = 'Confirmer et Enregistrer ✓';
        verifStep = 2;
    } else {
        submitVerif();
    }
}

function prevVerifStep() {
    document.getElementById('verif-step1').style.display = 'block';
    document.getElementById('verif-step2').style.display = 'none';
    document.getElementById('btnPrev').style.display = 'none';
    document.getElementById('progressBar').style.width = '50%';
    document.getElementById('stepLabel').textContent = 'Étape 1 sur 2 — Identité officielle';
    document.getElementById('btnNext').textContent = 'Continuer →';
    verifStep = 1;
}

function submitVerif() {
    const btn = document.getElementById('btnNext');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    const form = document.getElementById('infoVerifForm');
    const data = new FormData(form);
    
    fetch('{{ route("info.verification.submit") }}', { 
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('infoVerificationModal'));
            modal.hide();
            Swal.fire({
                icon: 'success',
                title: 'Merci !',
                text: 'Vos informations ont été validées.',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            btn.disabled = false;
            btn.textContent = 'Confirmer et Enregistrer ✓';
            alert('Erreur lors de la sauvegarde. Veuillez réessayer.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Confirmer et Enregistrer ✓';
        console.error(err);
    });
}

function skipVerif() {
    bootstrap.Modal.getInstance(document.getElementById('infoVerificationModal')).hide();
}
</script>
@endif