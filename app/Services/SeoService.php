<?php

namespace App\Services;

use App\Data\UserData;
use App\Data\UserProfileData;
use App\Settings\SeoSettings;
use App\Settings\SiteSettings;
use Carbon\Carbon;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Honeystone\Seo\OpenGraph\ArticleProperties;
use Honeystone\Seo\OpenGraph\ProfileProperties;
use Packages\Article\Data\ArticleData;
use Packages\Category\Data\CategoryData;
use Packages\Category\Data\CategoryProfileData;
use Packages\Entry\Data\EntryData;
use Packages\News\Data\NewsData;
use Packages\Page\Data\PageData;
use Packages\Tag\Data\TagData;
use Packages\Tag\Data\TagProfileData;
use Spatie\SchemaOrg\Schema;

class SeoService
{
    protected SiteSettings $siteSettings;

    protected SeoSettings $seoSettings;

    public function __construct()
    {
        $this->siteSettings = SettingsService::getSiteSettings();
        $this->seoSettings  = SettingsService::getSeoSettings();
    }

    /**
     * Get SEO data for the homepage.
     */
    public function getHomeSEO(): BuildsMetadata
    {
        $schema = Schema::webPage()
            ->name(__('Home'))
            ->url(route('home.view'))
            ->description($this->seoSettings->meta_description)
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->logo(Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute()))
            );

        return $this->buildSeo([
            'title' => __('Home'),
            'url'   => route('home.view'),
        ])->openGraphProperty('og:updated_time', Carbon::now()->format('Uu'))->jsonLdImport($schema);
    }

    /**
     * Get SEO for a user profile detail page.
     */
    public function getUserSEO(UserData $user, UserProfileData $profile): BuildsMetadata
    {
        $schema = Schema::person()
            ->name($user->name)
            ->url(route('user.view', ['username' => $user->username]))
            ->description($profile->bio ?? '')
            ->image($user->avatar)
            ->memberOf(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->logo(Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute()))
            );

        return $this->buildSeo([
            'title'         => $user->name,
            'description'   => $profile->bio,
            'image'         => $user->avatar,
            'url'           => route('user.view', ['username' => $user->username]),
            'openGraphType' => new ArticleProperties(
                publishedTime: $user->created_at,
                modifiedTime: $user->created_at,
                expirationTime: null,
                author: new ProfileProperties(
                    firstName: $user->name,
                    lastName: null,
                    username: $user->username
                )
            ),
        ])->openGraphProperty('og:updated_time', $user->created_at->format('Uu'))->jsonLdImport($schema);
    }

    /**
     * Get SEO data for the user settings page.
     */
    public function getUserSettingsSEO(): BuildsMetadata
    {
        return $this->buildSeo([
            'title' => __('Settings'),
            'url'   => route('user.settings.view'),
        ]);
    }

    /**
     * Get SEO data for the mail verification page.
     */
    public function getMailVerificationSEO(): BuildsMetadata
    {
        return $this->buildSeo([
            'title' => __('Mail Verification'),
            'url'   => route('user.verification.view'),
        ]);
    }

    /**
     * Get SEO data for the reset password page.
     */
    public function getResetPasswordSEO(): BuildsMetadata
    {
        return $this->buildSeo([
            'title' => __('Reset Password'),
        ]);
    }

    /**
     * Get SEO data for the article detail page.
     */
    public function getArticleSEO(ArticleData $article, UserData $user): BuildsMetadata
    {
        $schema = Schema::article()
            ->headline($article->title)
            ->description($article->excerpt)
            ->datePublished($article->published_at)
            ->dateModified($article->published_at)
            ->author(
                Schema::person()->name($user->name)
            )
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->description($this->seoSettings->meta_description)
                    ->logo(
                        Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute())
                    )
            )
            ->mainEntityOfPage(
                Schema::webPage()->url(route('article.view', ['slug' => $article->slug]))
                    ->image($article->image['thumb'])
                    ->name($article->title)
                    ->description($article->excerpt)
            )
            ->image($article->image['thumb']);

        return $this->buildSeo([
            'title'         => $article->title,
            'description'   => $article->excerpt,
            'image'         => $article->image['thumb'],
            'url'           => route('article.view', ['slug' => $article->slug]),
            'openGraphType' => new ArticleProperties(
                publishedTime: $article->published_at,
                modifiedTime: $article->published_at,
                expirationTime: null,
                author: new ProfileProperties(
                    firstName: $user->name,
                    lastName: null,
                    username: $user->username
                )
            ),
        ])->openGraphProperty('og:updated_time', $article->published_at->format('Uu'))->jsonLdImport($schema);
    }

    /**
     * Get SEO data for the article create page.
     */
    public function getArticleCreateSEO(): BuildsMetadata
    {
        return $this->buildSeo([
            'title' => __('Create Article'),
        ]);
    }

    /**
     * Get SEO data for the article edit page.
     */
    public function getArticleEditSEO(): BuildsMetadata
    {
        return $this->buildSeo([
            'title' => __('Edit Article'),
        ]);
    }

    /**
     * Get SEO data for the search page.
     */
    public function getSearchSEO(string $q): BuildsMetadata
    {
        return $this->buildSeo([
            'title' => $q,
        ]);
    }

    /**
     * Get SEO data for the entry detail page.
     */
    public function getEntrySEO(EntryData $entry, UserData $user): BuildsMetadata
    {
        $schema = Schema::article()
            ->description($entry->content)
            ->datePublished($entry->published_at)
            ->dateModified($entry->published_at)
            ->author(
                Schema::person()->name($user->name)
            )
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->description($this->seoSettings->meta_description)
                    ->logo(
                        Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute())
                    )
            )
            ->mainEntityOfPage(
                Schema::webPage()->url(route('entry.view', ['slug' => $entry->slug]))
                    ->description($entry->content)
            );

        return $this->buildSeo([
            'description'   => $entry->content,
            'url'           => route('entry.view', ['slug' => $entry->slug]),
            'openGraphType' => new ArticleProperties(
                publishedTime: $entry->published_at,
                modifiedTime: $entry->published_at,
                expirationTime: null,
                author: new ProfileProperties(
                    firstName: $user->name,
                    lastName: null,
                    username: $user->username
                )
            ),
        ])->openGraphProperty('og:updated_time', $entry->published_at->format('Uu'))->jsonLdImport($schema);
    }

    /**
     * Get SEO data for the page detail page.
     */
    public function getPageSEO(PageData $page, UserData $user): BuildsMetadata
    {
        $schema = Schema::webPage()
            ->headline($page->title)
            ->description($page->excerpt)
            ->datePublished($page->published_at)
            ->dateModified($page->published_at)
            ->author(
                Schema::person()->name($user->name)
            )
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->description($this->seoSettings->meta_description)
                    ->logo(
                        Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute())
                    )
            )
            ->image($page->image['thumb']);

        return $this->buildSeo([
            'title'         => $page->title,
            'description'   => $page->excerpt,
            'image'         => $page->image['thumb'],
            'url'           => route('page.view', ['slug' => $page->slug]),
            'openGraphType' => new ArticleProperties(
                publishedTime: $page->published_at,
                modifiedTime: $page->published_at,
                expirationTime: null,
                author: new ProfileProperties(
                    firstName: $user->name,
                    lastName: null,
                    username: $user->username
                )
            ),
        ])->openGraphProperty('og:updated_time', $page->published_at->format('Uu'))->jsonLdImport($schema);
    }

    /**
     * Get SEO data for the news detail page.
     */
    public function getNewsSEO(NewsData $news, UserData $user): BuildsMetadata
    {
        $schema = Schema::newsArticle()
            ->headline($news->title)
            ->description($news->excerpt)
            ->datePublished($news->published_at)
            ->dateModified($news->published_at)
            ->author(
                Schema::person()->name($user->name)
            )
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->description($this->seoSettings->meta_description)
                    ->logo(
                        Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute())
                    )
            )
            ->mainEntityOfPage(
                Schema::webPage()->url(route('news.view', ['slug' => $news->slug]))
                    ->image($news->image)
                    ->name($news->title)
                    ->description($news->excerpt)
            )
            ->image($news->image);

        return $this->buildSeo([
            'title'         => $news->title,
            'description'   => $news->excerpt,
            'image'         => $news->image,
            'url'           => route('news.view', ['slug' => $news->slug]),
            'openGraphType' => new ArticleProperties(
                publishedTime: $news->published_at,
                modifiedTime: $news->published_at,
                expirationTime: null,
                author: new ProfileProperties(
                    firstName: $user->name,
                    lastName: null,
                    username: $user->username
                )
            ),
        ])->openGraphProperty('og:updated_time', $news->published_at->format('Uu'))->jsonLdImport($schema);
    }

    /**
     * Get SEO data for the tag detail page.
     */
    public function getTagSeo(TagData $tag, TagProfileData $profile): BuildsMetadata
    {
        $schema = Schema::collectionPage()
            ->name($tag->name)
            ->description($profile->description ?? '')
            ->url(route('tag.view', ['slug' => $tag->slug]))
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->description($this->seoSettings->meta_description)
                    ->logo(
                        Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute())
                    )
            );

        return $this->buildSeo([
            'title'       => $tag->name,
            'description' => $profile->description,
            'url'         => route('tag.view', ['slug' => $tag->slug]),
            'image'       => null,
        ])->jsonLdImport($schema);
    }

    /**
     * Get SEO data for the category detail page.
     *
     * @return BuildsMetadata
     */
    public function getCategorySeo(CategoryData $category, CategoryProfileData $profile)
    {
        $schema = Schema::collectionPage()
            ->name($category->name)
            ->description($profile->description ?? '')
            ->url(route('category.view', ['slug' => $category->slug]))
            ->publisher(
                Schema::organization()
                    ->name($this->siteSettings->title)
                    ->description($this->seoSettings->meta_description)
                    ->logo(
                        Schema::imageObject()->url($this->siteSettings->getLightLogoUrlAttribute())
                    )
            );

        return $this->buildSeo([
            'title'       => $category->name,
            'description' => $profile->description,
            'url'         => route('category.view', ['slug' => $category->slug]),
            'image'       => null,
        ])->jsonLdImport($schema);
    }

    /**
     * Build SEO object with meta, Open Graph, Twitter, and JSON-LD.
     *
     * @param array<string, mixed> $data
     */
    private function buildSeo(array $data): BuildsMetadata
    {
        $seo = seo();

        if (isset($data['title']) && $data['title']) {
            $seo->title($data['title'])
                ->metaTitle($data['title'])
                ->twitterTitle($data['title'])
                ->openGraphTitle($data['title'])
                ->jsonLdName($data['title']);
        }

        if (isset($data['description']) && $data['description']) {
            $seo->description($data['description'])
                ->metaDescription($data['description'])
                ->twitterDescription($data['description'])
                ->openGraphDescription($data['description'])
                ->jsonLdDescription($data['description']);
        }

        if (isset($data['url']) && $data['url']) {
            $seo->url($data['url'])
                ->canonical($data['url'])
                ->metaCanonical($data['url'])
                ->openGraphUrl($data['url'])
                ->jsonLdUrl($data['url']);
        }

        if (isset($data['image']) && $data['image']) {
            $seo->images($data['image'])
                ->twitterImage($data['image'])
                ->openGraphImage($data['image'])
                ->jsonLdImage($data['image']);
        }

        if (isset($data['openGraphType']) && $data['openGraphType']) {
            $seo->openGraphType($data['openGraphType']);
        }

        // $seo->openGraphProperty("og:logo", $this->siteSettings->getLightLogoUrlAttribute());

        return $seo;
    }
}
