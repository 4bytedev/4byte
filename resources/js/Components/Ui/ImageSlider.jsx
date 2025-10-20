import { PhotoProvider, PhotoView } from "react-photo-view";
import { Swiper, SwiperSlide } from "swiper/react";
import { cn } from "@/Lib/Utils";
import { X } from "lucide-react";

export function ImageSlider({
	medias,
	onRemove,
	className,
	spaceBetween,
	slidesPerView,
	renderSlider,
}) {
	return (
		<PhotoProvider>
			<Swiper
				spaceBetween={spaceBetween}
				slidesPerView={slidesPerView}
				pagination={{ clickable: true }}
				navigation
				className="overflow-hidden"
			>
				{medias.map((media, index) => (
					<SwiperSlide key={index} className="relative">
						{onRemove && (
							<X
								onClick={() => onRemove(media.id)}
								className="absolute top-1 right-1 z-10 bg-black bg-opacity-50 text-white p-1 hover:bg-opacity-75 cursor-pointer"
							/>
						)}
						<PhotoView src={media.image}>
							{renderSlider ? (
								renderSlider(media, index)
							) : (
								<img
									src={media.image}
									srcSet={media.srcset}
									className={cn(
										"h-28 w-full object-cover cursor-pointer",
										className,
									)}
								/>
							)}
						</PhotoView>
					</SwiperSlide>
				))}
			</Swiper>
		</PhotoProvider>
	);
}
