<?php
/**
 * SEO-оптимизатор
 */
namespace RAAS\CMS;

/**
 * Класс SEO-оптимизатора
 */
class SeoOptimizer
{
    /**
     * Рекомендованная максимальная длина тега TITLE
     */
    const META_TITLE_RECOMMENDED_LIMIT = 60;

    /**
     * Строгая рекомендованная максимальная длина тега TITLE
     */
    const META_TITLE_STRICT_LIMIT = 75;

    /**
     * Максимальное рекомендованное количество слов тега TITLE
     */
    const META_TITLE_WORDS_LIMIT = 13;

    /**
     * Рекомендованная максимальная длина тега DESCRIPTION
     */
    const META_DESCRIPTION_RECOMMENDED_LIMIT = 140;

    /**
     * Строгая рекомендованная максимальная длина тега DESCRIPTION
     */
    const META_DESCRIPTION_STRICT_LIMIT = 155;
}
