<style lang="scss">
.design-item {
    border: 1px solid #ddd;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    &__image {
        display: block;
        width: 100%;
        img {
            background: #ddd;
            display: block;
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: contain;
        }
    }
    &__text {
        color: gray;
        font-size: 10px;
        text-align: center;
        margin-block: 0.5rem;
        flex-grow: 1;
        line-height: 1;
    }
    &__controls {
        text-align: center;
    }
}
</style>