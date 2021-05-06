<template>
  <div>
    <div class="content">
      <div class="container-fluid">
        <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
        <div
          class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20"
        >
          <p class="_title0">
            Categories
            <Button @click="addModal = true"
              ><Icon type="md-add" />Add category</Button
            >
          </p>
          <div class="_overflow _table_div">
            <table class="_table">
              <!-- TABLE TITLE -->
              <tr>
                <th>ID</th>
                <th>Category name</th>
                <th>Created at</th>
                <th>Action</th>
              </tr>
              <!-- ITEMS -->
              <!-- <tr v-for="(tag, i) in tags" :key="i">
                <td>{{ tag.id }}</td>
                <td class="_table_name">
                  {{ tag.tagName }}
                </td>
                <td>{{ tag.created_at }}</td>
                <td>
                  <Button
                    type="info"
                    size="small"
                    @click="showEditModal(tag, i)"
                    >Edit</Button
                  >
                  <Button
                    type="error"
                    size="small"
                    @click="showDeletingModal(tag, i)"
                    :loading="tag.isDeleting"
                    >Delete</Button
                  >
                </td>
              </tr> -->
            </table>
          </div>
        </div>

        <!-- Category adding model -->
        <Modal
          v-model="addModal"
          title="Add category"
          :mask-closable="false"
          :closable="false"
        >
          <Input v-model="data.tagName" placeholder="Add category name" />
          <div class="space"></div>
          <!-- Upload file(image) -->
          <Upload
            ref="uploads"
            type="drag"
            :headers="{
              'x-csrf-token': token,
              'X-Requested-With': 'XMLHttpRequest',
            }"
            :on-success="handleSuccess"
            :on-error="handleError"
            :format="['jpg', 'jpeg', 'png']"
            :on-format-error="handleFormatError"
            :max-size="2048"
            :on-exceeded-size="handleMaxSize"
            action="/app/upload"
          >
            <div style="padding: 20px 0">
              <Icon
                type="ios-cloud-upload"
                size="52"
                style="color: #3399ff"
              ></Icon>
              <p>Click or drag files here to upload</p>
            </div>
          </Upload>

          <!-- Show image with icon delete -->
          <div class="demo-upload-list" v-if="data.iconImage">
            <img :src="`/uploads/${data.iconImage}`" />
            <div class="demo-upload-list-cover">
              <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
            </div>
          </div>
          
          <!-- Custom Footer Modal -->
          <div slot="footer">
            <Button type="default" @click="addModal = false">Close</Button>
            <Button
              type="primary"
              @click="addCategory"
              :disabled="isAdding"
              :loading="isAdding"
              >{{ isAdding ? "Adding.." : "Add category" }}</Button
            >
          </div>
        </Modal>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      data: {
        iconImage: "",
        categoryName: "",
      },

      addModal: false,
      editModal: false,
      isAdding: false,
      categories: [],
      editData: {
        categoryName: "",
      },
      index: -1,
      showDeleteModal: false,
      isDeleting: false,
      deleteItem: {},
      deletingIndex: -1,
      token: "",
    };
  },
  methods: {
    async addCategory() {
      if (this.data.categoryName.trim() == "")
        return this.swr("Category name is required");
      if (this.data.iconImage.trim() == "")
        return this.swr("Icon image is required");
      const res = await this.callApi("post", "app/create_category", this.data);
      if (res.status === 201) {
        this.categories.unshift(res.data);
        this.s("Category has been added successfully!");
        this.addModal = false;
        this.data.categoryName = "";
        this.data.iconImage = "";
      } else {
        if (res.status === 420) {
          if (res.data.errors.ca) {
            this.i(res.data.errors.categoryName[0]);
          }
          if (res.data.errors.ca) {
            this.i(res.data.errors.iconImage[0]);
          }
        } else {
          this.swr();
        }
      }
    },
    handleSuccess(res, file) {
        this.data.iconImage = res;
    },
    handleError(res, file) {
      console.log("res", res);
      console.log("file", file);
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc: `${
          file.errors.file.length
            ? file.errors.file.length[0]
            : "something went wrong!"
        }`,
      });
    },
    handleFormatError(file) {
      this.$Notice.warning({
        title: "The file format is incorrect",
        desc:
          "File format of " +
          file.name +
          " is incorrect, please select jpg or png.",
      });
    },
    handleMaxSize(file) {
      this.$Notice.warning({
        title: "Exceeding file size limit",
        desc: "File  " + file.name + " is too large, no more than 2M.",
      });
    },
    async deleteImage() {
      let image = this.data.iconImage;
      this.data.iconImage = "";
      this.$refs.uploads.clearFiles();
      const res = await this.callApi("post", "app/delete_image", {
        imageName: image,
      });
      if (res.status != 200) {
        this.data.imageImage = image;
        this.swr();
      }
    },
  },
  async created() {
    this.token = window.Laravel.csrfToken;
  },
  /* async created() {
    this.token = window.Laravel.csrfToken;
    const res = await this.callApi("get", "app/get_tag");
    if (res.status === 200) {
      this.tags = res.data;
    } else {
      this.swr();
    }
  }, */
};
</script>

<style>
.demo-upload-list {
  display: inline-block;
  width: 60px;
  height: 60px;
  text-align: center;
  line-height: 60px;
  border: 1px solid transparent;
  border-radius: 4px;
  overflow: hidden;
  background: #fff;
  position: relative;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
  margin-right: 4px;
}
.demo-upload-list img {
  width: 100%;
  height: 100%;
}
.demo-upload-list-cover {
  display: none;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.6);
}
.demo-upload-list:hover .demo-upload-list-cover {
  display: block;
}
.demo-upload-list-cover i {
  color: #fff;
  font-size: 20px;
  cursor: pointer;
  margin: 0 2px;
}
</style>
