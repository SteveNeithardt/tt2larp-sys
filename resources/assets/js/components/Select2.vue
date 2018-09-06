<template>
	<select>
		<slot></slot>
	</select>
</template>

<script>
	export default {
		props: ['options', 'value'],
		mounted: function() {
			var vm = this
			$(this.$el)
				.select2({ data: this.options })
				.val(this.value)
				.trigger('change')
				.on('change', function() {
					vm.$emit('input', this.value)
				})
		},
		watch: {
			value: function(value) {
				$(this.$el)
					.val(value)
					.trigger('change')
			},
			options: function(options) {
				$(this.$el).empty().select2(options);
			},
		},
		destroyed: function() {
			$(this.$el).off().select2('destroy');
		},
	}
</script>
