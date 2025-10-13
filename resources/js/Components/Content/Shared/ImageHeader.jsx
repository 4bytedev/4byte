export function ImageHeader({ image, title }) {
	if (!image) return null;
	return (
		<div className="relative h-40 w-full overflow-hidden">
			<img
				src={image.image}
				srcSet={image.srcset}
				alt={title}
				className="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-105"
			/>
			<div className="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent" />
		</div>
	);
}
