import { Image } from "@/types";
import { useEffect, useState } from "react";

function Carousel({ images }: { images: Image[] }) {
    const [selectedImage, setSelectedImage] = useState<Image | null>(images[0] ?? null);

    useEffect(() => {
        if (images.length > 0) {
            setSelectedImage(images[0]);
        } else {
            setSelectedImage(null);
        }
    }, [images]);

    return (
        <div className="flex items-start gap-8">
            <div className="flex flex-col items-center gap-2 py-4">
                {images.map((image) => (
                    <button
                        key={image.id}
                        onClick={() => setSelectedImage(image)}
                        className={"border-2 " +
                            (selectedImage?.id === image.id ? "border-indigo-500" : "hover:border-indigo-500")}
                    >
                        <img src={image.thumb} alt={image.id.toString()} className="w-[50px]" />
                    </button>
                ))}
            </div>

            <div className="carousel w-full">
                {selectedImage ? (
                    <div className="carousel-item w-full">
                        <img src={selectedImage.large} alt={selectedImage.id.toString()} className="w-full" />
                    </div>
                ) : (
                    <div className="carousel-item w-full flex items-center justify-center text-gray-500">
                        No image available
                    </div>
                )}
            </div>
        </div>
    );
}

export default Carousel;
