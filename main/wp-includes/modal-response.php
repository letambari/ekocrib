<!-- The Modal -->
<div id="success-alert-modal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content modal-filled bg-success">
      <div class="modal-body p-4">
        <div class="text-center">
          <i class="dripicons-checkmark h1"></i>
          <h4 class="mt-2">Well Done!</h4>
          <p class="mt-3"><span id="modalContent" style="color: white;"></span></p>
          <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Ok</button>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Button to open the modal -->
<button id="modal-trigger" style="display: none;" data-bs-toggle="modal" data-bs-target="#success-alert-modal"></button>




<!-- Info Alert Modal -->

<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body p-4">
				<div class="text-center">
					<i class="dripicons-warning h1 text-warning"></i>
					<h4 class="mt-2">Heads up!</h4>
					<p class="mt-3" id="modalContent"></p>
					<button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Try Again</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Button to open the modal -->
<button id="modal-trigger2" style="display: none;" data-bs-toggle="modal" data-bs-target="#warning-alert-modal"></button>