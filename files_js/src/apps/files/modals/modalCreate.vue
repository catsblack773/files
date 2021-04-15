<template>
	<modal
		name="create"
		:minWidth="minWidth"
		:minHeight="minHeight"
		:width="width"
		:height="height"
		:adaptive="true"
	>
		<header>
			<div class="header-wrapp">
				<div class="header-title">{{title}}</div>
				<div class="header-action-wrapp">
					<div class="header-close icon el-icon-close" @click="$modal.hide('create')"></div>
				</div>
			</div>
		</header>
		<main class="main-wrapp">
			<el-form ref="form-create">
				<div class="el-form-item">
					<label class="el-form-item__label">Введите название папки</label>
					<div class="el-form-item__content">
						<div class="el-input el-input--medium">
							<autofocus-input @onUpdate="onUpdate" />
						</div>
					</div>
				</div>
				<el-form-item style="text-align: right;margin-bottom:0">
					<el-button type="primary" size="small" @click="queryCreateFolder()" plain>Создать папку</el-button>
				</el-form-item>
			</el-form>
		</main>
	</modal>
</template>

<script>
import autofocusInput from './autofocusInput'

export default {
	props: {
		title: {
			type: String,
			required: true
		},
		width: {
			type: Number,
			required: false,
			default: 300
		},
		height: {
			type: Number,
			required: false,
			default: 200
		},
		minWidth: {
			type: Number,
			required: false,
			default: 300
		},
		minHeight: {
			type: Number,
			required: false,
			default: 200
		},
		path: {
			type: String,
			required: true
		}
	},
	data () {
		return {
			createNameFolder: null
		}
	},
	components: {
		autofocusInput
	},
	methods: {
		async queryCreateFolder () {
			const data = new FormData()
			data.append('folder', this.createNameFolder)
			data.append('path', this.path)
			await this.$http(`${this.$store.getters.domans}/files/create-folder/`, {
				method: 'POST',
				headers: {'is-json': true},
				data
			})
				.then(response => {
					if (response.data.res === true) {
						this.$modal.hide('create')

						this.$emit('reLoad', {
							folders: response.data.folders,
							files: response.data.files,
							path: response.data.path
						})
						
						this.$message({
							message: response.data.message,
							type: 'success'
						})
					} else if (response.data.res === false) {
						this.$message.error(response.data.message)
					}
				})
				.catch(err => {
					this.$message.error(err)
				})
		},
		onUpdate (name) {
			this.createNameFolder = name
		}
	}
}
</script>

<style scoped>
	.main-wrapp {
		padding: 0 15px;
	}
	.modal-content footer,
	.modal-content main,
	.modal-content header {
		width: 100%;
		font-size: 1rem;
		box-sizing: border-box;
	}
	.modal-content header {
		border-bottom: 1px solid #ccc;
	}
	.header-title {
		width: calc(100% - 80px);
		padding-left: 15px;
	}
	.header-action-wrapp {
		padding-right: 15px;
	}
	.modal-content footer {
		display: flex;
		align-items: center;
		height: 30px;
		border-top: 1px solid #ccc;
	}
	.header-wrapp {
		height: 30px;
		font-size: 14px;
	}
	.modal-content .information-panel,
	.modal-content .header-wrapp {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}
	.modal-content .information-panel {
		padding: 0 14px;
		font-size: 13px;
	}
	.header-action-wrapp .icon {
		margin-right: 6px;
		font-weight: 900;
		cursor: pointer;
	}
	.header-action-wrapp .icon:last-child {
		margin-right: 0;
	}
	.header-action-wrapp .icon:hover {
		color: #555;
	}
	.modal-content .content {
		height: 100%;
	}
</style>