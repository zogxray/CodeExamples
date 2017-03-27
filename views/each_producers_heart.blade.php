<div class="c-producers__item">
    <div class="c-producers__item__avatar">
        <a v-bind:href="producer.url">
            <img v-bind:src="producer.avatar_image_url" v-bind:alt="producer.title">
        </a>
    </div>
    <div class="c-producers__item__text">
        <h4 class="c-producers__item__title">
            <a v-bind:href="producer.url">@{{producer.title}}</a>
            <small v-if="producer.edit">
                <a v-bind:href="producer.edit" style="color: #ad2f6b;">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
            </small>
        </h4>

        <p class="c-producers__item__location">
            <a v-bind:href="producer.url">@{{producer.country.title}}</a>
        </p>
    </div>
    <div class="c-producers__item__info">
        <table>
            <tr>
                <td>{{trans('producers.uses')}}</td>
                <td><strong>@{{producer.masters_count}}</strong></td>
            </tr>
            <tr>
                <td>{{trans('producers.works')}}</td>
                <td><strong>@{{producer.works_count}}</strong></td>
            </tr>
        </table>
    </div>
    <a v-bind:href="producer.url" class="c-producers__item__image">
        <img v-bind:src="producer.square_product_url" v-bind:alt="producer.title">
    </a>
</div>