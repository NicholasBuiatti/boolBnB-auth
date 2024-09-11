@extends('layouts.navBar')

@section('content')
	<table class="table table-hover table-responsive">
		<thead class="">
			<tr class="table-dark text-center">
				<th scope="col"></th>
				<th scope="col">Nome</th>
				<th scope="col">Data di fine Sponsorizzazione</th>
				<th scope="col">Visibile</th>
				<th scope="col">Dettagli/Modifica</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($catalogue as $apartment)
				<tr class="align-middle text-center">
					<th scope="row">
						<img class="object-fit" src="{{ $apartment->image }}" alt="" style="height: 10rem; width:12rem">
					</th>
					<td class="fs-2 fw-bolder">{{ $apartment->title }}</td>
					<td>
						@if ($apartment->lastSponsorship)
							<p>{{ $apartment->lastSponsorship->pivot->ending_date }}</p>
						@endif
						<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
							data-bs-target="#offcanvasRight-{{ $apartment->id }}" aria-controls="offcanvasRight">
							Sponsorizza
						</button>

						<div class="offcanvas w-100 offcanvas-end"
							style="background: linear-gradient(130deg, #fff6e7, #a8c2cb); tabindex="-1"
							id="offcanvasRight-{{ $apartment->id }}" aria-labelledby="offcanvasRightLabel-{{ $apartment->id }}">
							<div class="offcanvas-header">
								<h1 class="mx-auto">Seleziona una Sponsorizzazione</h1>
								<button type="button" class="btn-close text-reset ms-0" data-bs-dismiss="offcanvas" aria-label="Close"></button>
							</div>
							<div class="offcanvas-body">
								<div class="container">


									<form id="payment-form-{{ $apartment->id }}" method="POST"
										action="{{ route('sponsorships.payment', ['apartment' => $apartment->id]) }}">
										@csrf
										<div class="row justify-content-around">
											@foreach ($sponsorships as $sponsorship)
												<div class="card border-info mb-3 col-10 col-lg-3 shadow p-3 mb-5 bg-body rounded"
													style="{{ $sponsorship->name == 'Basic' ? 'background: linear-gradient(130deg, #f8ebd1, #b89a6e);' : '' }}
													{{ $sponsorship->name == 'Premium' ? 'background: linear-gradient(130deg, #f0f4f8, #c0c0c0);' : '' }}
													 {{ $sponsorship->name == 'Elite'
													    ? 'background: linear-gradient(130deg, #fff5e4, #f2c27f);
																																																																																																																																																																																																																'
													    : '' }}">
													<div class="card-header text-uppercase fs-3 fw-bold">{{ $sponsorship->name }}</div>
													<div class="card-body">
														<p>{{ $sponsorship->description }}</p>
														<input type="radio" name="sponsorship_id" id="sponsorship_{{ $sponsorship->id }}"
															value="{{ $sponsorship->id }}" data-amount="{{ $sponsorship->price }}">
														<label for="sponsorship_{{ $sponsorship->id }}">
															{{ $sponsorship->name }} - €{{ $sponsorship->price }}
														</label>
													</div>
												</div>
											@endforeach

										</div>
										<div id="dropin-container-{{ $apartment->id }}" class="col-10 col-lg-4 mx-auto shadow p-3 mb-5 rounded"></div>

										<button type="submit" class="btn btn-custom">Paga ora</button>
									</form>

								</div>
							</div>
						</div>

					</td>
					<td>
						@if ($apartment->is_visible == 1)
							<i class="fa-solid fa-check fs-3 text-success"></i>
						@else
							<i class="fa-solid fa-xmark text-danger"></i>
						@endif
					</td>
					<td>
						<a class="btn btn-primary my-2" href="{{ route('apartments.show', $apartment->id) }}">
							<i class="fa-solid fa-eye"></i>
						</a>
						<a class="btn btn-secondary" href="{{ route('apartments.edit', $apartment->id) }}">
							<i class="fa-solid fa-pen"></i>
						</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{{ $catalogue->links('pagination::bootstrap-5') }}

	<script src="https://js.braintreegateway.com/web/dropin/1.31.0/js/dropin.min.js"></script>
	<script>
		document.querySelectorAll('button[data-bs-toggle="offcanvas"]').forEach(function(button) {
			button.addEventListener('click', function() {
				// Recupera l'id dell'appartamento dal data-bs-target
				var apartmentId = this.getAttribute('data-bs-target').split('-').pop();

				// Seleziona il form e il dropin-container per l'appartamento specifico
				var form = document.querySelector('#payment-form-' + apartmentId);
				var dropinContainer = document.querySelector('#dropin-container-' + apartmentId);

				// Inizializza Braintree Drop-in per questo specifico appartamento
				braintree.dropin.create({
					authorization: "{{ $clientToken }}",
					container: dropinContainer,
					locale: 'it' // Imposta la lingua italiana
				}, function(createErr, instance) {
					if (createErr) {
						console.error(createErr);
						return;
					}

					form.addEventListener('submit', function(event) {
						event.preventDefault();

						instance.requestPaymentMethod(function(err, payload) {
							if (err) {
								console.error(err);
								return;
							}

							// Aggiungi il nonce nel form e invia
							var nonceInput = document.createElement('input');
							nonceInput.setAttribute('type', 'hidden');
							nonceInput.setAttribute('name', 'payment_method_nonce');
							nonceInput.setAttribute('value', payload.nonce);
							form.appendChild(nonceInput);

							form.submit();
						});
					});
				});
			});
		});
	</script>

	<style>
		/* CSS PER PULSANTE CUSTOM */

		.btn-custom {
			background: linear-gradient(130deg, #f7c6c7, #f9e1d1);
			border: none;
			color: #333;
			font-weight: bold;
			border-radius: 30px;
			padding: 10px 20px;
			transition: background 0.3s, transform 0.3s;
		}

		.btn-custom:hover {
			background: linear-gradient(130deg, #f4a4a5, #f7d0c5);
			transform: scale(1.05);
		}

		.btn-custom:focus,
		.btn-custom:active {
			box-shadow: none;
		}
	</style>
@endsection
