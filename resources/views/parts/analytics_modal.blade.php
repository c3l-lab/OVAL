<!-- analytics modal content-->
<div id="analytics_modal" class="modal modal-wide fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-close" aria-hidden="true"></i>
				</button>
				<h4 class="modal-title"> OVAL Analytics Dashboard </h4>
			</div>
			<div class="modal-body">
				<form role="form" data-toggle="validator">
					<div class="form-group">
	 					<table class="analytics_wrap">
    
						</table>
					</div>

					<div class="form-group">
	 					<table class="analytics_extra_wrap">

						</table>
					</div>

					<div class="form-group">
						<div class="chart_canvas"></div>
					</div>

					<div class="form-buttons" style="text-align:center;">
						<button type="button" class="btn btn-link" id="download_csv"><i class="fa fa-save"></i> Download Report</button>
						<button type="button" class="btn btn-link" id="download_detail_csv" style="display:none;"><i class="fa fa-save"></i> Download Detail</button>
						<!-- <button type="button" class="btn btn-link pull-right"><i class="fa fa-pencil-square-o" data-dismiss="modal"></i></button> -->
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- end analytics modal content-->