import { useState } from "react";
import { Calendar, Share2, Check } from "lucide-react";
import { Avatar, AvatarFallback, AvatarImage } from "@/Components/Ui/Avatar";
import { Button } from "@/Components/Ui/Form/Button";
import { Separator } from "@/Components/Ui/Separator";
import { UserProfileHover } from "@/Components/Common/UserProfileHover";
import MarkdownRenderer from "@/Components/Common/MarkdownRenderer";

export default function PagePage({ page }) {
	const [isCopied, setIsCopied] = useState(false);

	const handleShare = () => {
		if (navigator.share) {
			navigator.share({
				title: page.title,
				text: page.excerpt,
				url: window.location.href,
			});
		} else {
			navigator.clipboard.writeText(window.location.href);
			setIsCopied(true);
			setTimeout(() => {
				setIsCopied(false);
			}, 1500);
		}
	};

	return (
		<div className="container mx-auto px-4 py-8">
			<div className="max-w-4xl mx-auto">
				{/* Page Header */}
				<div className="mb-8">
					<h1 className="text-4xl font-bold mb-6">{page.title}</h1>

					<div className="flex items-center justify-between">
						<div className="flex items-center space-x-4">
							<UserProfileHover username={page.user.username}>
								<div className="flex items-center space-x-3 cursor-pointer">
									<Avatar className="h-12 w-12">
										<AvatarImage
											src={page.user.avatar.image}
											alt={page.user.name}
										/>
										<AvatarFallback>
											{page.user.name
												.split(" ")
												.map((n) => n[0])
												.join("")}
										</AvatarFallback>
									</Avatar>
									<div>
										<p className="font-medium">{page.user.name}</p>
										<p className="text-sm text-muted-foreground">
											@{page.user.username}
										</p>
									</div>
								</div>
							</UserProfileHover>

							<div className="flex items-center space-x-4 text-sm text-muted-foreground">
								<div className="flex items-center space-x-1">
									<Calendar className="h-4 w-4" />
									<span>{new Date(page.published_at).toLocaleDateString()}</span>
								</div>
							</div>
						</div>

						<div className="flex items-center space-x-2">
							<Button variant="outline" size="sm" onClick={handleShare}>
								{isCopied ? (
									<Check className="h-4 w-4" />
								) : (
									<Share2 className="h-4 w-4" />
								)}
							</Button>
						</div>
					</div>
				</div>

				<Separator className="mb-8" />

				{/* Article Content */}
				<MarkdownRenderer content={page.content} />

				<Separator className="mb-8" />

				{/* Comments Section */}
				{/* <div className="mb-8">
          <h3 className="text-xl font-semibold mb-6 flex items-center">
            <MessageCircle className="h-5 w-5 mr-2" />
            Comments ({comments.length})
          </h3>
          
          <Card className="mb-6">
            <CardContent className="p-4">
              <Textarea
                placeholder="Share your thoughts..."
                value={comment}
                onChange={(e) => setComment(e.target.value)}
                className="mb-4"
                rows={3}
              />
              <div className="flex justify-end">
                <Button disabled={!comment.trim()}>Post Comment</Button>
              </div>
            </CardContent>
          </Card>
          
          <div className="space-y-6">
            {comments.map((comment) => (
              <Card key={comment.id}>
                <CardContent className="p-6">
                  <div className="flex items-start space-x-4">
                    <UserProfileHover user={{
                      ...comment.author,
                      bio: "",
                      joinedDate: "2022-01-01",
                      followers: 100,
                      following: 50,
                      articles: 10,
                      tags: []
                    }}>
                      <Avatar className="h-10 w-10 cursor-pointer">
                        <AvatarImage src={comment.author.avatar} alt={comment.author.name} />
                        <AvatarFallback>
                          {comment.author.name.split(' ').map(n => n[0]).join('')}
                        </AvatarFallback>
                      </Avatar>
                    </UserProfileHover>
                    <div className="flex-1">
                      <div className="flex items-center space-x-2 mb-2">
                        <span className="font-medium">{comment.author.name}</span>
                        <span className="text-sm text-muted-foreground">{comment.author.role}</span>
                        <span className="text-sm text-muted-foreground">•</span>
                        <span className="text-sm text-muted-foreground">
                          {formatTimeAgo(comment.timestamp)}
                        </span>
                      </div>
                      <p className="text-muted-foreground mb-3">{comment.content}</p>
                      <div className="flex items-center space-x-4">
                        <Button variant="ghost" size="sm">
                          <Heart className="h-4 w-4 mr-1" />
                          {comment.likes}
                        </Button>
                        <Button variant="ghost" size="sm">
                          Reply
                        </Button>
                      </div>
                      
                      {comment.replies && comment.replies.length > 0 && (
                        <div className="mt-4 pl-4 border-l-2 border-muted space-y-4">
                          {comment.replies.map((reply) => (
                            <div key={reply.id} className="flex items-start space-x-3">
                              <Avatar className="h-8 w-8">
                                <AvatarImage src={reply.author.avatar} alt={reply.author.name} />
                                <AvatarFallback className="text-xs">
                                  {reply.author.name.split(' ').map(n => n[0]).join('')}
                                </AvatarFallback>
                              </Avatar>
                              <div className="flex-1">
                                <div className="flex items-center space-x-2 mb-1">
                                  <span className="font-medium text-sm">{reply.author.name}</span>
                                  <span className="text-xs text-muted-foreground">
                                    {formatTimeAgo(reply.timestamp)}
                                  </span>
                                </div>
                                <p className="text-sm text-muted-foreground mb-2">{reply.content}</p>
                                <Button variant="ghost" size="sm">
                                  <Heart className="h-3 w-3 mr-1" />
                                  {reply.likes}
                                </Button>
                              </div>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div> */}

				{/* Related Articles */}
				{/* <div>
          <h3 className="text-xl font-semibold mb-6">Related Articles</h3>
          <div className="grid gap-4 md:grid-cols-2">
            {relatedArticles.map((related, index) => (
              <Card key={index} className="hover:shadow-lg transition-shadow cursor-pointer">
                <CardContent className="p-6">
                  <h4 className="font-semibold mb-2">{related.title}</h4>
                  <div className="flex items-center justify-between text-sm text-muted-foreground">
                    <span>by {related.author}</span>
                    <span>{related.readTime}</span>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div> */}

				{/* Navigation */}
				{/* <div className="flex justify-between mt-12 pt-8 border-t">
          <Button variant="outline">
            <ChevronLeft className="h-4 w-4 mr-2" />
            Previous Article
          </Button>
          <Button variant="outline">
            Next Article
            <ChevronRight className="h-4 w-4 ml-2" />
          </Button>
        </div> */}
			</div>
		</div>
	);
}
